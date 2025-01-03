<?php
/**
 * Namespace functions.
 *
 * @package quark-ship-decks
 */

namespace Quark\ShipDecks;

use WP_Post;

use function Quark\CabinCategories\get_cabin_categories_data;

const POST_TYPE   = 'qrk_ship_deck';
const CACHE_KEY   = POST_TYPE;
const CACHE_GROUP = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type.
	add_action( 'init', __NAMESPACE__ . '\\register_ship_deck_post_type' );

	// Other hooks. Assigning non-standard priority to avoid race conditions with ACF.
	add_action( 'save_post', __NAMESPACE__ . '\\bust_post_cache', 11 );

	// Add meta keys to be translated while content sync.
	add_filter( 'qrk_translation_meta_keys', __NAMESPACE__ . '\\translate_meta_keys' );

	// Admin stuff.
	if ( is_admin() || ( defined( 'WP_CLI' ) && true === WP_CLI ) ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/ship-decks.php';
	}
}

/**
 * Register Ship Deck post type.
 *
 * @return void
 */
function register_ship_deck_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Ship Decks',
			'singular_name'      => 'Ship Deck',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Ship Deck',
			'edit_item'          => 'Edit Ship Deck',
			'new_item'           => 'New Ship Deck',
			'view_item'          => 'View Ship Deck',
			'search_items'       => 'Search Ship Decks',
			'not_found'          => 'No Ship Decks found',
			'not_found_in_trash' => 'No Ship Decks found in Trash',
			'parent_item_colon'  => 'Parent Ship Deck:',
			'menu_name'          => 'Ship Decks',
		],
		'public'              => false,
		'show_in_rest'        => false,
		'menu_icon'           => 'dashicons-button',
		'hierarchical'        => false,
		'supports'            => [
			'title',
			'editor',
		],
		'show_ui'             => true,
		'show_in_menu'        => 'edit.php?post_type=qrk_ship',
		'show_in_nav_menus'   => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => false,
		'capability_type'     => 'post',
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
	do_action( 'qe_ship_deck_cache_busted', $post_id );
}

/**
 * Get a Ship Deck post.
 *
 * @param int $post_id Ship Deck Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     post_meta: mixed[],
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
			'post'      => $cached_value['post'],
			'post_meta' => $cached_value['post_meta'] ?? [],
		];
	}

	// Get post.
	$post = get_post( $post_id );

	// Check for post.
	if ( ! $post instanceof WP_Post || POST_TYPE !== $post->post_type ) {
		return [
			'post'      => null,
			'post_meta' => [],
		];
	}

	// Build data.
	$data = [
		'post'      => $post,
		'post_meta' => [],
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
 * Get Ship Deck data.
 *
 * @param int $deck_id Ship Deck ID.
 *
 * @return array{
 *     id: string,
 *     title: string,
 *     image_id: int,
 *     vertical_image_id: int,
 *     description: string,
 *     cabin_options:array{
 *         id: string,
 *         title: string,
 *         image_id: int,
 *         description: mixed,
 *         details: array{
 *             size_from: string,
 *             size_to: string,
 *             occupancy_from: string,
 *             occupancy_to: string,
 *             bed_configuration: string,
 *             class: string,
 *             location: string,
 *          }
 *     }[],
 *     public_spaces:  array<int, array{
 *         title?: string,
 *         description?: string,
 *         image?: int,
 *     }>,
 * }
 */
function get_deck_data( int $deck_id = 0 ): array {
	// Get ship ID.
	if ( 0 === $deck_id ) {
		$deck_id = absint( get_the_ID() );
	}

	// Initialize deck data.
	$decks_data = [
		'id'                => '',
		'title'             => '',
		'image_id'          => 0,
		'vertical_image_id' => 0,
		'description'       => '',
		'cabin_options'     => [],
		'public_spaces'     => [],
	];

	// Get the deck.
	$deck = get( $deck_id );

	// Get the post and post meta.
	$deck_post = $deck['post'];
	$deck_meta = $deck['post_meta'];

	// Check for post.
	if ( ! $deck_post instanceof WP_Post || empty( $deck_meta ) ) {
		return $decks_data;
	}

	// Prepare public spaces data.
	$public_spaces = prepare_public_spaces( $deck_meta );

	// Prepare Cabin Options data.
	$cabin_options = [];

	// Check if we have cabin categories.
	if ( ! empty( $deck_meta['cabin_categories'] ) && is_array( $deck_meta['cabin_categories'] ) ) {
		$cabin_options_ids = array_map( 'absint', $deck_meta['cabin_categories'] );
		$cabin_options     = get_cabin_options( $cabin_options_ids );
	}

	// Prepare deck data.
	$decks_data = [
		'id'                => $deck_post->post_name,
		'title'             => strval( $deck_meta['deck_name'] ?? '' ),
		'image_id'          => absint( $deck_meta['deck_plan_image'] ?? 0 ),
		'vertical_image_id' => absint( $deck_meta['vertical_deck_plan_image'] ?? 0 ),
		'description'       => strval( apply_filters( 'the_content', $deck_post->post_content ) ),
		'cabin_options'     => $cabin_options,
		'public_spaces'     => $public_spaces,
	];

	// Return deck data.
	return $decks_data;
}

/**
 * Prepare public spaces data.
 *
 * @param mixed[] $deck_meta The deck meta.
 *
 * @return array<int, array{
 *    title?: string,
 *    description?: string,
 *    image?: int,
 * }>
 */
function prepare_public_spaces( array $deck_meta = [] ): array {
	// Check if we have public spaces.
	if ( empty( $deck_meta ) ) {
		return [];
	}

	// Prepare public spaces data.
	$public_spaces = [];

	// Search for public spaces meta keys and store its values.
	foreach ( $deck_meta as $key => $value ) {
		// Check if this is a public space meta key.
		if ( false === strpos( $key, 'public_spaces_' ) ) {
			continue;
		}

		// Split the key into parts.
		$key_parts = explode( '_', $key );
		$key_name  = end( $key_parts );
		$key_index = absint( $key_parts[2] );

		// Assign the value based on the key name.
		switch ( $key_name ) {
			// Title.
			case 'title':
				$public_spaces[ $key_index ]['title'] = strval( $value );
				break;

			// Description.
			case 'description':
				$public_spaces[ $key_index ]['description'] = strval( apply_filters( 'the_content', $value ) );
				break;

			// Image.
			case 'image':
				$public_spaces[ $key_index ]['image'] = absint( $value );
				break;
		}
	}

	// Return public spaces data.
	return $public_spaces;
}

/**
 * Get Cabin Options data.
 *
 * @param int[] $cabin_options_ids The cabin options IDs.
 *
 * @return array{
 *    id: string,
 *    title: string,
 *    image_id: int,
 *    description: mixed,
 *    details: array{
 *        size_from: string,
 *        size_to: string,
 *        occupancy_from: string,
 *        occupancy_to: string,
 *        bed_configuration: string,
 *        class: string,
 *        location: string,
 *    }
 * }[]
 */
function get_cabin_options( array $cabin_options_ids = [] ): array {
	// Check if we have cabin options.
	if ( empty( $cabin_options_ids ) ) {
		return [];
	}

	// Get the cabin options.
	$cabin_options = [];

	// Loop through the cabin options IDs.
	foreach ( $cabin_options_ids as $cabin_option_id ) {
		// Get the cabin option.
		$cabin_options_data = get_cabin_categories_data( $cabin_option_id );

		// Check if we have cabin option data.
		if ( ! empty( $cabin_options_data ) ) {
			$cabin_options[] = $cabin_options_data;
		}
	}

	// Return cabin options data.
	return $cabin_options;
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
		'deck_name'                     => 'string',
		'deck_plan_image'               => 'attachment',
		'vertical_deck_plan_image'      => 'attachment',
		'cabin_categories'              => 'post',
		'public_spaces_\d+_title'       => 'string',
		'public_spaces_\d+_image'       => 'attachment',
		'public_spaces_\d+_description' => 'string',
	];

	// Return meta keys to be translated.
	return array_merge( $meta_keys, $extra_keys );
}
