<?php
/**
 * Namespace functions.
 *
 * @package quark-blog
 */

namespace Quark\Blog;

use WP_Post;
use WP_Term;

use function Quark\Blog\Authors\get as get_post_authors;
use function yoast_get_primary_term_id;

const POST_TYPE        = 'post';
const CACHE_KEY        = POST_TYPE;
const CACHE_GROUP      = POST_TYPE;
const WORDS_PER_MINUTE = 230; // using the same value from Drupal - https://github.com/mtownsend5512/read-time/.

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Enable primary term.
	add_filter( 'travelopia_primary_term_taxonomies', __NAMESPACE__ . '\\primary_term_taxonomies', 10, 2 );

	// Other hooks.
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\calculate_post_reading_time', 10, 3 );
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\bust_post_cache' );

	// Breadcrumbs.
	add_filter( 'travelopia_breadcrumbs_ancestors', __NAMESPACE__ . '\\breadcrumbs_ancestors' );

	// Admin stuff.
	if ( is_admin() || ( defined( 'WP_CLI' ) && true === WP_CLI ) ) {
		add_filter( 'post_type_labels_' . POST_TYPE, __NAMESPACE__ . '\\update_blog_posts_admin_menu_label' );

		// Custom fields.
		require_once __DIR__ . '/../custom-fields/blog.php';
	}

	// Other hooks.
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\bust_post_cache' );
}

/**
 * Update blog post admin menu name to "Blog Posts".
 *
 * @param object|null $labels Original labels.
 *
 * @return object
 */
function update_blog_posts_admin_menu_label( object $labels = null ): object {
	// Update menu name.
	if ( isset( $labels->menu_name ) ) {
		$labels->menu_name = 'Blog Posts';
	}

	// Return updated labels.
	return (object) $labels;
}

/**
 * Bust cache when a post is saved.
 *
 * @param int $post_id Post ID.
 *
 * @return void
 */
function bust_post_cache( int $post_id = 0 ): void {
	// Delete the post cache.
	wp_cache_delete( CACHE_KEY . "_$post_id", CACHE_GROUP );

	// Trigger action to clear cache for this post.
	do_action( 'qe_post_cache_busted', $post_id );
}

/**
 * Get a Blog Post.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     permalink: string,
 *     post_thumbnail: int,
 *     post_meta: mixed[],
 *     post_taxonomies: mixed[],
 * }
 */
function get( int $post_id = 0 ): array {
	// Get post ID.
	if ( 0 === $post_id ) {
		$post_id = absint( get_the_ID() );
	}

	// Check for cached version.
	$cache_key    = CACHE_KEY . "_$post_id";
	$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

	// Check for cached value.
	if ( is_array( $cached_value ) && ! empty( $cached_value['post'] ) && $cached_value['post'] instanceof WP_Post ) {
		return [
			'post'            => $cached_value['post'],
			'permalink'       => $cached_value['permalink'] ?? '',
			'post_thumbnail'  => $cached_value['post_thumbnail'] ?? 0,
			'post_meta'       => $cached_value['post_meta'] ?? [],
			'post_taxonomies' => $cached_value['post_taxonomies'] ?? [],
		];
	}

	// Get post.
	$post = get_post( $post_id );

	// Return empty array fields if post type does not match or not an instance of WP_Post.
	if ( ! $post instanceof WP_Post || POST_TYPE !== $post->post_type ) {
		return [
			'post'            => null,
			'permalink'       => '',
			'post_thumbnail'  => 0,
			'post_meta'       => [],
			'post_taxonomies' => [],
		];
	}

	// Build data.
	$data = [
		'post'            => $post,
		'permalink'       => strval( get_permalink( $post ) ? : '' ),
		'post_thumbnail'  => absint( get_post_thumbnail_id( $post ) ? : 0 ),
		'post_meta'       => [],
		'post_taxonomies' => [],
	];

	// Get all post meta.
	$meta = get_post_meta( $post->ID );

	// Check for post meta.
	if ( ! empty( $meta ) && is_array( $meta ) ) {
		$data['post_meta'] = array_filter(
			array_map(
				fn( $value ) => maybe_unserialize( $value[0] ?? '' ),
				$meta
			),
			fn( $key ) => ! str_starts_with( $key, '_' ),
			ARRAY_FILTER_USE_KEY
		);
	}

	// Taxonomy terms.
	global $wpdb;
	$taxonomy_terms = $wpdb->get_results(
		$wpdb->prepare(
			"
			SELECT
				t.*,
				tt.taxonomy,
				tt.description,
				tt.parent
			FROM
				$wpdb->term_relationships AS tr
			LEFT JOIN
				$wpdb->term_taxonomy AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
			LEFT JOIN
				$wpdb->terms AS t ON t.term_id = tt.term_taxonomy_id
			WHERE
				tr.object_id = %d
			ORDER BY
				t.name ASC
			",
			[
				$post->ID,
			]
		),
		ARRAY_A
	);

	// Check for taxonomy terms.
	if ( ! empty( $taxonomy_terms ) ) {
		foreach ( $taxonomy_terms as $taxonomy_term ) {
			if ( ! array_key_exists( $taxonomy_term['taxonomy'], $data['post_taxonomies'] ) ) {
				$data['post_taxonomies'][ $taxonomy_term['taxonomy'] ] = [];
			}
			$data['post_taxonomies'][ $taxonomy_term['taxonomy'] ][] = $taxonomy_term;
		}
	}

	// Set cache and return data.
	wp_cache_set( $cache_key, $data, CACHE_GROUP );

	// Return data.
	return $data;
}

/**
 * Add category to primary term taxonomies.
 *
 * @param mixed[] $taxonomies Array of taxonomy objects.
 * @param string  $post_type  Post type.
 *
 * @return mixed[]|array{
 *     string,
 * }
 */
function primary_term_taxonomies( array $taxonomies = [], string $post_type = '' ): array {
	// Add category to primary term taxonomies.
	if ( POST_TYPE === $post_type ) {
		// Add category to primary term taxonomies.
		$taxonomies[] = 'category';
	}

	// Return taxonomies.
	return $taxonomies;
}

/**
 * Calculate reading time for a post.
 *
 * @param int          $post_id Post ID.
 * @param WP_Post|null $post    Post object.
 *
 * @return void
 */
function calculate_post_reading_time( int $post_id = 0, WP_Post $post = null ): void {
	// Bail if not a post or post type does not match.
	if ( ! $post instanceof WP_Post || POST_TYPE !== $post->post_type ) {
		return;
	}

	// Get words count.
	$words_count = str_word_count( wp_strip_all_tags( $post->post_content ) );

	// Calculate reading time.
	$minutes = ceil( $words_count / WORDS_PER_MINUTE );

	// Save data to post meta.
	update_post_meta( $post_id, 'read_time_minutes', absint( $minutes ) );
}

/**
 * Get data for blog post cards.
 *
 * @param int[] $post_ids Post IDs.
 *
 * @return array<mixed>{
 *    post: array<mixed>,
 *    title: string,
 *    permalink: string,
 *    featured_image: int,
 *    excerpt: string,
 *    authors: mixed[],
 *    read_time: int,
 *    taxonomies: array<mixed>,
 * }[]
 */
function get_cards_data( array $post_ids = [] ): array {
	// Check if post ids exist.
	if ( empty( $post_ids ) ) {
		return [];
	}

	// Initialize data.
	$data = [];

	// Loop through the post ids.
	foreach ( $post_ids as $post_id ) {
		$post = get( $post_id );

		// Get blog author ids.
		$blog_author_ids = (array) $post['post_meta']['blog_authors'] ?: [];

		// Initialize authors data.
		$authors_data = [];

		// Loop through blog author ids.
		foreach ( $blog_author_ids as $blog_author_id ) {
			$authors_data[] = get_post_authors( absint( $blog_author_id ) );
		}

		// Build post data.
		$post_data = [
			'post'           => $post['post'] ?: [],
			'title'          => $post['post']?->post_title ?? '',
			'permalink'      => $post['permalink'] ?: '',
			'featured_image' => $post['post_thumbnail'] ?: 0,
			'excerpt'        => get_the_excerpt( $post['post']?->ID ),
			'authors'        => $authors_data,
			'read_time'      => array_key_exists( 'read_time_minutes', $post['post_meta'] ) ? absint( $post['post_meta']['read_time_minutes'] ) : 0,
			'taxonomies'     => $post['post_taxonomies'] ?: [],
		];

		// Add blog post data to array.
		$data[] = $post_data;
	}

	// Return data.
	return $data;
}

/**
 * Get blog post author info.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *      authors : array{
 *          array{
 *           title: string,
 *           image_id: int,
 *       }
 *      }|array{},
 *      duration : int,
 * }
 */
function get_blog_post_author_info( int $post_id = 0 ): array {
	// Initialize attributes.
	$attributes = [
		'authors'  => [],
		'duration' => 0,
	];

	// Get post ID.
	if ( 0 === $post_id ) {
		$post_id = absint( get_the_ID() );
	}

	// Get post.
	$post = get( $post_id );

	// Bail if post does not exist or not an instance of WP_Post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $attributes;
	}

	// Get blog author ids.
	$blog_author_ids = ! empty( $post['post_meta']['blog_authors'] ) ? (array) $post['post_meta']['blog_authors'] : [];

	// Loop through blog author ids.
	foreach ( $blog_author_ids as $blog_author_id ) {
		$authors_data = get_post_authors( absint( $blog_author_id ) );

		// Break the loop if authors data is not empty.
		if ( ! empty( $authors_data['post'] ) ) {
			$attributes['authors'][] = [
				'title'    => $authors_data['post']->post_title,
				'image_id' => $authors_data['post_thumbnail'],
			];
		}
	}

	// Set attributes.
	$attributes['duration'] = ! empty( $post['post_meta']['read_time_minutes'] ) ? absint( $post['post_meta']['read_time_minutes'] ) : 0;

	// Return attributes.
	return $attributes;
}

/**
 * Breadcrumbs ancestors for this post type.
 *
 * @param mixed[] $breadcrumbs Breadcrumbs.
 *
 * @return mixed[]
 */
function breadcrumbs_ancestors( array $breadcrumbs = [] ): array {
	// Check if current query is for this post type.
	if ( ! ( is_singular( POST_TYPE ) || is_author() || is_category() ) ) {
		return $breadcrumbs;
	}

	// Get archive page.
	$blog_archive_page = absint( get_option( 'page_for_posts', 0 ) );

	// Get it's title and URL for breadcrumbs if it's set.
	if ( ! empty( $blog_archive_page ) ) {
		$breadcrumbs[] = [
			'title' => get_the_title( $blog_archive_page ),
			'url'   => get_permalink( $blog_archive_page ),
		];
	}

	// Get post ID.
	$post_id = get_the_ID();

	// Get primary category for post.
	if ( ! $post_id ) {
		return $breadcrumbs;
	}

	// Get primary category.
	$primary_category_id = yoast_get_primary_term_id( 'category', $post_id );

	// Get term.
	if ( ! is_int( $primary_category_id ) ) {
		return $breadcrumbs;
	}

	// Get primary category term.
	$primary_category = get_term( $primary_category_id, 'category' );

	// Add primary category to breadcrumbs.
	if ( $primary_category instanceof WP_Term ) {
		$breadcrumbs[] = [
			'title' => $primary_category->name,
			'url'   => get_term_link( $primary_category ),
		];
	}

	// Return updated breadcrumbs.
	return $breadcrumbs;
}
