<?php
/**
 * Namespace functions.
 *
 * @package quark-adventure-options
 */

namespace Quark\AdventureOptions;

use WP_Post;

use function Quark\Core\prepare_content_with_blocks;

const POST_TYPE                 = 'qrk_adventure_option';
const ADVENTURE_OPTION_CATEGORY = 'qrk_adventure_option_category';
const CACHE_KEY                 = POST_TYPE;
const CACHE_GROUP               = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type and taxonomies.
	add_action( 'init', __NAMESPACE__ . '\\register_adventure_option_post_type' );
	add_action( 'init', __NAMESPACE__ . '\\register_adventure_option_category_taxonomy' );

	// Opt into stuff.
	add_filter( 'qe_adventure_options_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_destination_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );

	// Layout.
	add_action( 'template_redirect', __NAMESPACE__ . '\\layout' );

	// Cache Purge.
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\bust_post_cache' );

	// Admin stuff.
	if ( is_admin() ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/adventure-option-category.php';
	}
}

/**
 * Register adventure option post type.
 *
 * @return void
 */
function register_adventure_option_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Adventure Options',
			'singular_name'      => 'Adventure Option',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Adventure Option',
			'edit_item'          => 'Edit Adventure Option',
			'new_item'           => 'New Adventure Option',
			'view_item'          => 'View Adventure Option',
			'search_items'       => 'Search Adventure Options',
			'not_found'          => 'No Adventure Options found',
			'not_found_in_trash' => 'No Adventure Options found in Trash',
			'parent_item_colon'  => 'Parent Adventure Option:',
			'menu_name'          => 'Adventure Options',
		],
		'public'              => true,
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-location-alt',
		'hierarchical'        => false,
		'supports'            => [
			'title',
			'editor',
			'excerpt',
			'thumbnail',
			'revisions',
		],
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'publicly_queryable'  => true,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => [
			'slug'       => 'adventure-option',
			'with_front' => false,
		],
		'capability_type'     => 'post',
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
}

/**
 * Register Adventure Options taxonomy.
 *
 * @return void
 */
function register_adventure_option_category_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Adventure Option Categories',
		'singular_name'              => 'Adventure Option Category',
		'search_items'               => 'Search Adventure Option Categories',
		'popular_items'              => 'Popular Adventure Option Categories',
		'all_items'                  => 'All Adventure Option Categories',
		'parent_item'                => 'Parent Adventure Option Category',
		'parent_item_colon'          => 'Parent Adventure Option Category:',
		'edit_item'                  => 'Edit Adventure Option Category',
		'update_item'                => 'Update Adventure Option Category',
		'add_new_item'               => 'Add New Adventure Option Category',
		'new_item_name'              => 'New Adventure Option Category',
		'separate_items_with_commas' => 'Separate Adventure Option Categories with commas',
		'add_or_remove_items'        => 'Add or remove Adventure Option Categories',
		'choose_from_most_used'      => 'Choose from the most used Adventure Option Categories',
		'menu_name'                  => 'Adventure Option Categories',
	];

	// Prepare args for registering taxonomy.
	$args = [
		'labels'            => $labels,
		'public'            => false,
		'show_in_nav_menus' => false,
		'show_ui'           => false,
		'show_tagcloud'     => false,
		'show_admin_column' => true,
		'hierarchical'      => true,
		'rewrite'           => false,
		'query_var'         => true,
		'capabilities'      => [],
		'show_in_rest'      => true,
	];

	// Register taxonomy.
	register_taxonomy( ADVENTURE_OPTION_CATEGORY, (array) apply_filters( 'qe_adventure_options_taxonomy_post_types', [] ), $args );
}

/**
 * Opt into stuff.
 *
 * @param string[] $post_types Post types.
 *
 * @return string[]
 */
function opt_in( array $post_types = [] ): array {
	// Append Adventure Option post type for taxonomy.
	$post_types[] = POST_TYPE;

	// Return modified array.
	return $post_types;
}

/**
 * Layout for the post type.
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
 * Busts cache for this post type.
 *
 * @param int $post_id Post ID.
 *
 * @return void
 */
function bust_post_cache( int $post_id = 0 ): void {
	// Clear cache for this post.
	wp_cache_delete( CACHE_KEY . "_$post_id", CACHE_GROUP );

	// Trigger action to clear cache for this post.
	do_action( 'qe_adventure_option_post_cache_busted', $post_id );
}

/**
 * Get an Adventure Option.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     permalink: string,
 *     post_thumbnail: int,
 *     post_meta: mixed[],
 *     post_taxonomies: mixed[]
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

	// Check for post.
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
		'permalink'       => get_permalink( $post ) ?: '',
		'post_thumbnail'  => get_post_thumbnail_id( $post ) ?: 0,
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
