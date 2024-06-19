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
use function Quark\Core\prepare_content_with_blocks;

const POST_TYPE   = 'post';
const CACHE_KEY   = POST_TYPE;
const CACHE_GROUP = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Layout.
	add_action( 'template_redirect', __NAMESPACE__ . '\\layout' );

	// Enable primary term.
	add_filter( 'travelopia_primary_term_taxonomies', __NAMESPACE__ . '\\primary_term_taxonomies', 10, 2 );

	// Breadcrumbs.
	add_filter( 'travelopia_breadcrumbs_ancestors', __NAMESPACE__ . '\\breadcrumbs_ancestors' );

	// Admin stuff.
	if ( is_admin() ) {
		add_filter( 'post_type_labels_' . POST_TYPE, __NAMESPACE__ . '\\update_blog_posts_admin_menu_label' );

		// Custom fields.
		require_once __DIR__ . '/../custom-fields/blog.php';
	}

	// Other hooks.
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\bust_post_cache' );
}

/**
 * Layout for this post type.
 *
 * @return void
 */
function layout(): void {
	// Add single layout if viewing a single post.
	if ( is_singular( POST_TYPE ) ) {
		add_filter( 'quark_front_end_data', __NAMESPACE__ . '\\layout_single' );
	}
}

/**
 * Layout: Single.
 *
 * @param mixed[] $data Front-end data.
 *
 * @return mixed[]
 */
function layout_single( array $data = [] ): array {
	// Get post.
	$page = get();

	// Bail if post does not exist or not an instance of WP_Post.
	if ( empty( $page['post'] ) || ! $page['post'] instanceof WP_Post ) {
		return $data;
	}

	// Layout.
	$data['layout'] = 'single';

	// Build data.
	$data['data'] = array_merge( $data['data'] ?? [], $page );

	// Post content.
	$data['data']['post_content'] = $page['post']->post_content;

	// Prepare blocks.
	prepare_content_with_blocks( $data['data']['post_content'] );

	// Return front-end data.
	return $data;
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
 * Get data for blog post cards.
 *
 * @param int[] $post_ids Post IDs.
 *
 * @return array<mixed>{
 *      post: array|WP_Post,
 *      title: string,
 *      permalink: string,
 *      featured_image: integer,
 *      read_time: integer,
 *      authors: array,
 *      taxonomies: array,
 *  }[]
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
 * Breadcrumbs ancestors for this post type.
 *
 * @param mixed[] $breadcrumbs Breadcrumbs.
 *
 * @return mixed[]
 */
function breadcrumbs_ancestors( array $breadcrumbs = [] ): array {
	// Check if current query is for this post type.
	if ( !( is_singular( POST_TYPE ) || is_author() || is_category() ) ) {
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
	$primary_category_id = \yoast_get_primary_term_id( 'category', $post_id );

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
