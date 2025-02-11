<?php
/**
 * Namespace functions.
 *
 * @package quark-policy-pages
 */

namespace Quark\PolicyPages;

use WP_Post;

const POST_TYPE   = 'qrk_agreement';
const CACHE_KEY   = POST_TYPE;
const CACHE_GROUP = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type.
	add_action( 'init', __NAMESPACE__ . '\\register_agreement_post_type' );

	// Other hooks. Assigning non-standard priority to avoid race conditions with ACF.
	add_action( 'save_post', __NAMESPACE__ . '\\bust_post_cache', 11 );

	// Add meta keys to be translated while content sync.
	add_filter( 'qrk_translation_meta_keys', __NAMESPACE__ . '\\translate_meta_keys' );

	// Admin stuff.
	if ( is_admin() ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/policy-pages.php';
	}
}

/**
 * Register Policy Pages post type.
 *
 * @return void
 */
function register_agreement_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Terms and Conditions / Policy Pages',
			'singular_name'      => 'Terms and Conditions / Policy Page',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Terms and Conditions / Policy Page',
			'edit_item'          => 'Edit Terms and Conditions / Policy Page',
			'new_item'           => 'New Terms and Conditions / Policy Page',
			'view_item'          => 'View Terms and Conditions / Policy Page',
			'search_items'       => 'Search Terms and Conditions / Policy Pages',
			'not_found'          => 'No Terms and Conditions / Policy Pages found',
			'not_found_in_trash' => 'No Terms and Conditions / Policy Pages found in Trash',
			'parent_item_colon'  => 'Parent Terms and Conditions / Policy Page:',
			'menu_name'          => 'Terms and Conditions / Policy Pages',
		],
		'public'              => true,
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-printer',
		'hierarchical'        => false,
		'supports'            => [
			'title',
			'editor',
			'excerpt',
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
			'slug'       => 'terms-and-conditions',
			'with_front' => false,
		],
		'capability_type'     => 'page',
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
}

/**
 * Busts cache for this post type.
 *
 * @param int $post_id Post ID.
 *
 * @return void
 */
function bust_post_cache( int $post_id = 0 ): void {
	// Get post type.
	$post_type = get_post_type( $post_id );

	// Check for post type.
	if ( POST_TYPE !== $post_type ) {
		return;
	}

	// Clear cache for this post.
	wp_cache_delete( CACHE_KEY . "_$post_id", CACHE_GROUP );

	// Trigger action to clear cache for this post.
	do_action( 'qe_agreement_post_cache_busted', $post_id );
}

/**
 * Get an agreement Post.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     post_meta: mixed[],
 *     permalink: string|false,
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
		// Return cached value.
		return [
			'post'      => $cached_value['post'],
			'post_meta' => $cached_value['post_meta'] ?? [],
			'permalink' => get_permalink( $cached_value['post']->ID ),
		];
	}

	// Get post.
	$post = get_post( $post_id );

	// Check for post.
	if ( ! $post instanceof WP_Post || POST_TYPE !== $post->post_type ) {
		return [
			'post'      => null,
			'post_meta' => [],
			'permalink' => false,
		];
	}

	// Build data.
	$data = [
		'post'      => $post,
		'post_meta' => [],
		'permalink' => get_permalink( $post->ID ),
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

	// Set cache and return data.
	wp_cache_set( $cache_key, $data, CACHE_GROUP );

	// Return data.
	return $data;
}

/**
 * Translate meta keys.
 *
 * @param array<string, string> $meta_keys Meta keys.
 *
 * @return array<string, string|string[]>
 */
function translate_meta_keys( array $meta_keys = [] ): array {
	// Meta keys for translation.
	$extra_keys = [
		'alternate_title'          => 'string',
		'marketing_option_icon'    => 'attachment',
		'marketing_option_summary' => 'string',
	];

	// Return meta keys to be translated.
	return array_merge( $meta_keys, $extra_keys );
}
