<?php
/**
 * Namespace functions.
 *
 * @package quark-ships
 */

namespace Quark\Ships;

use WP_Post;

const POST_TYPE              = 'qrk_ship';
const SHIP_CATEGORY_TAXONOMY = 'qrk_ship_categories';
const CACHE_KEY              = POST_TYPE;
const CACHE_GROUP            = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type and taxonomy.
	add_action( 'init', __NAMESPACE__ . '\\register_ship_post_type' );
	add_action( 'init', __NAMESPACE__ . '\\register_ship_categories_taxonomy' );

	// Opt into stuff.
	add_filter( 'qe_ship_category_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );

	// Other hooks.
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\bust_post_cache' );
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\bust_ship_code_lookup_cache' );

	// Admin stuff.
	if ( is_admin() ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/ships.php';
	}
}

/**
 * Register Ship post type.
 *
 * @return void
 */
function register_ship_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Ships',
			'singular_name'      => 'Ship',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Ship',
			'edit_item'          => 'Edit Ship',
			'new_item'           => 'New Ship',
			'view_item'          => 'View Ship',
			'search_items'       => 'Search Ships',
			'not_found'          => 'No Ships found',
			'not_found_in_trash' => 'No Ships found in Trash',
			'parent_item_colon'  => 'Parent Ship:',
			'menu_name'          => 'Ships',
		],
		'public'              => true,
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-sos',
		'hierarchical'        => false,
		'supports'            => [
			'title',
			'editor',
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
			'slug'       => 'expedition-ships',
			'with_front' => false,
		],
		'capability_type'     => 'post',
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
}

/**
 * Register Ship Categories taxonomy.
 *
 * @return void
 */
function register_ship_categories_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Ship Categories',
		'singular_name'              => 'Ship Category',
		'search_items'               => 'Search Ship Categories',
		'popular_items'              => 'Popular Ship Categories',
		'all_items'                  => 'All Ship Categories',
		'parent_item'                => 'Parent Ship Category',
		'parent_item_colon'          => 'Parent Ship Category:',
		'edit_item'                  => 'Edit Ship Category',
		'update_item'                => 'Update Ship Category',
		'add_new_item'               => 'Add New Ship Category',
		'new_item_name'              => 'New Ship Category',
		'separate_items_with_commas' => 'Separate Ship Categories with commas',
		'add_or_remove_items'        => 'Add or remove Ship Categories',
		'choose_from_most_used'      => 'Choose from the most used Ship Categories',
		'menu_name'                  => 'Ship Categories',
	];

	// Prepare args for registering taxonomy.
	$args = [
		'labels'            => $labels,
		'public'            => false,
		'show_in_nav_menus' => false,
		'show_ui'           => true,
		'show_tagcloud'     => false,
		'show_admin_column' => true,
		'hierarchical'      => true,
		'rewrite'           => false,
		'query_var'         => true,
		'capabilities'      => [],
		'show_in_rest'      => true,
	];

	// Register taxonomy.
	register_taxonomy( SHIP_CATEGORY_TAXONOMY, (array) apply_filters( 'qe_ship_category_taxonomy_post_types', [] ), $args );
}

/**
 * Opt into stuff.
 *
 * @param string[] $post_types Post types.
 *
 * @return string[]
 */
function opt_in( array $post_types = [] ): array {
	// Append Ship post type for taxonomy.
	$post_types[] = POST_TYPE;

	// Return modified array.
	return $post_types;
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
	do_action( 'qe_ship_cache_busted', $post_id );
}

/**
 * Get a Ship page.
 *
 * @param int $post_id Ship Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     permalink: string,
 *     post_meta: mixed[],
 *     block_attrs: mixed[],
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
			'post_meta'       => $cached_value['post_meta'] ?? [],
			'block_attrs'     => $cached_value['block_attrs'] ?? [],
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
			'post_meta'       => [],
			'block_attrs'     => [],
			'post_taxonomies' => [],
		];
	}

	// Get Ship block attrs.
	$block_attrs = parse_block_attributes( $post );

	// Build data.
	$data = [
		'post'            => $post,
		'permalink'       => strval( get_permalink( $post ) ? : '' ),
		'post_meta'       => [],
		'block_attrs'     => $block_attrs,
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
 * Convert ship code to ship post ID.
 *
 * @param string $ship_code The ship code.
 *
 * @return int
 */
function get_id_from_ship_code( string $ship_code = '' ): int {
	// Check for cached version.
	$cache_key = CACHE_KEY . '_all_ships';
	$ships     = wp_cache_get( $cache_key, CACHE_GROUP );

	// If cache not set, lets build it with a DB query.
	if ( empty( $ships ) ) {
		// Post Meta.
		global $wpdb;
		$ships = $wpdb->get_results(
			$wpdb->prepare(
				"
			SELECT
				m.*
			FROM
				$wpdb->postmeta AS m
			RIGHT JOIN
				$wpdb->posts AS p ON m.post_id = p.ID
			WHERE
				m.meta_key = 'ship_id'
			AND
				p.post_type = %s
			",
				POST_TYPE
			),
			ARRAY_A
		);

		// Set cache and return data.
		wp_cache_set( $cache_key, $ships, CACHE_GROUP );
	}

	// Find the ship code.
	foreach ( $ships as $ship ) {
		if ( $ship_code === $ship['meta_value'] ) {
			return $ship['post_id'];
		}
	}

	// Not found, return 0.
	return 0;
}

/**
 * Bust Ship code lookup cache.
 *
 * @return void
 */
function bust_ship_code_lookup_cache(): void {
	// Delete the code cache.
	wp_cache_delete( CACHE_KEY . '_all_ships', CACHE_GROUP );

	// Trigger action to clear cache.
	do_action( 'qe_ship_code_lookup_cache_busted' );
}

/**
 * Get ship data.
 *
 * @param int $ship_id Ship ID.
 *
 * @return array{}|array{
 *    name: string,
 *    title: string,
 *    permalink: string,
 *    description: string,
 *    related_decks: int[]|array{},
 * }
 */
function get_ship_data( int $ship_id = 0 ): array {
	// Get the ship data.
	$ship = get( $ship_id );

	// Get the post and post meta.
	$ship_post = $ship['post'];
	$ship_meta = $ship['post_meta'];

	// Check for post.
	if ( ! $ship_post instanceof WP_Post ) {
		return [];
	}

	// Prepare deck data.
	$decks_ids = [];

	// Get Decks associated with the ship.
	if ( ! empty( $ship_meta['related_decks'] ) && is_array( $ship_meta['related_decks'] ) ) {
		$decks_ids = array_map( 'absint', $ship_meta['related_decks'] );
	}

	// Return ship data.
	return [
		'name'          => $ship_post->post_name,
		'title'         => $ship_post->post_title,
		'permalink'     => $ship['permalink'],
		'description'   => strval( apply_filters( 'the_content', $ship_post->post_content ) ),
		'related_decks' => $decks_ids,
	];
}

/**
 * Parse the collage block attributes.
 *
 * @param WP_Post|null $post The post object.
 *
 * @return array{}|array{
 *     ship_collage: mixed[],
 *     ship_vessel_features_title: string[],
 *     ship_amenities_title: string[],
 * }
 */
function parse_block_attributes( WP_Post $post = null ): array {
	// Check if the post valid WP_Post.
	if ( empty( $post ) || ! $post instanceof WP_Post ) {
		return [];
	}

	// Parse blocks.
	$blocks = parse_blocks( $post->post_content );

	// Initialize collage attributes.
	$collage_attrs        = [];
	$ship_vessel_features = [];
	$ship_amenities       = [];

	// Loop through blocks to find the quark/collage block.
	foreach ( $blocks as $block ) {
		// Check if the block is quark/collage.
		if ( 'quark/collage' === $block['blockName'] ) {
			// Loop through inner blocks (quark/collage-media-item).
			if ( isset( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				// Loop through inner blocks to find the quark/collage-media-item block.
				foreach ( $block['innerBlocks'] as $inner_block ) {
					// Check if the block is quark/collage-media-item.
					if ( 'quark/collage-media-item' === $inner_block['blockName'] ) {
						// Retrieve attributes.
						$collage_attrs[] = $inner_block['attrs'];
					}
				}
			}
		}

		// Check if the block is quark/ship-vessel-features.
		if ( 'quark/ship-vessel-features' === $block['blockName'] ) {
			// Loop through inner blocks (quark/ship-vessel-features-card).
			if ( isset( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				// Loop through inner blocks to find the quark/ship-vessel-features-card block.
				foreach ( $block['innerBlocks'] as $inner_block ) {
					// Check if the block is quark/ship-vessel-features-card.
					if ( 'quark/ship-vessel-features-card' === $inner_block['blockName'] ) {
						// Check attributes are available.
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) && ! empty( $inner_block['attrs']['title'] ) ) {
							// Retrieve attributes.
							$ship_vessel_features[] = $inner_block['attrs']['title'];
						}
					}
				}
			}
		}

		// Check if the block is quark/ship-features-amenities.
		if ( 'quark/ship-features-amenities' === $block['blockName'] ) {
			// Loop through inner blocks (quark/ship-features-amenities-card).
			if ( isset( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				// Loop through inner blocks to find the quark/ship-features-amenities-card block.
				foreach ( $block['innerBlocks'] as $inner_block ) {
					// Check if the block is quark/ship-features-amenities-card.
					if ( 'quark/ship-features-amenities-card' === $inner_block['blockName'] ) {
						// Check attributes are available.
						if ( isset( $inner_block['attrs'] ) && is_array( $inner_block['attrs'] ) && ! empty( $inner_block['attrs']['title'] ) ) {
							// Retrieve attributes.
							$ship_amenities[] = $inner_block['attrs']['title'];
						}
					}
				}
			}
		}
	}

	// Return block attributes.
	return [
		'ship_collage'               => $collage_attrs,
		'ship_vessel_features_title' => $ship_vessel_features,
		'ship_amenities_title'       => $ship_amenities,
	];
}
