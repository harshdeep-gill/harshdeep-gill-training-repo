<?php
/**
 * Namespace functions.
 *
 * @package quark-inclusion-sets
 */

namespace Quark\InclusionSets;

use WP_Post;

const POST_TYPE                    = 'qrk_inclusion_set';
const INCLUSION_EXCLUSION_CATEGORY = 'qrk_inclusion_exclusion_category';
const CACHE_KEY                    = POST_TYPE;
const CACHE_GROUP                  = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type and taxonomies.
	add_action( 'init', __NAMESPACE__ . '\\register_inclusion_set_post_type' );
	add_action( 'init', __NAMESPACE__ . '\\register_inclusion_exclusion_category_taxonomy' );

	// Opt into stuff.
	add_filter( 'qe_inclusion_exclusion_category_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );

	// Other hooks. Assigning non-standard priority to avoid race conditions with ACF.
	add_action( 'save_post', __NAMESPACE__ . '\\bust_post_cache', 11 );

	// Add meta keys to be translated while content sync.
	add_filter( 'qrk_translation_meta_keys', __NAMESPACE__ . '\\translate_meta_keys' );

	// Admin stuff.
	if ( is_admin() ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/inclusion-exclusion-set.php';

		// Taxonomy menu position.
		add_action( 'admin_menu', __NAMESPACE__ . '\\set_taxonomy_menu_position' );
	}
}

/**
 * Register Inclusion Set post type.
 *
 * @return void
 */
function register_inclusion_set_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Inclusion Sets',
			'singular_name'      => 'Inclusion Set',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Inclusion Set',
			'edit_item'          => 'Edit Inclusion Set',
			'new_item'           => 'New Inclusion Set',
			'view_item'          => 'View Inclusion Set',
			'search_items'       => 'Search Inclusion Sets',
			'not_found'          => 'No Inclusion Sets found',
			'not_found_in_trash' => 'No Inclusion Sets found in Trash',
			'parent_item_colon'  => 'Parent Inclusion Set:',
			'menu_name'          => 'Inclusion Sets',
		],
		'public'              => false,
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-networking',
		'hierarchical'        => false,
		'supports'            => [
			'title',
		],
		'show_ui'             => true,
		'show_in_menu'        => 'edit.php?post_type=qrk_itinerary',
		'show_in_nav_menus'   => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'has_archive'         => false,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => false,
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
}

/**
 * Register Inclusion Exclusion Category taxonomy.
 *
 * @return void
 */
function register_inclusion_exclusion_category_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Inclusion Exclusion Set Categories',
		'singular_name'              => 'Sets Category',
		'search_items'               => 'Search Sets Categories',
		'popular_items'              => 'Popular Sets Categories',
		'all_items'                  => 'All Sets Categories',
		'parent_item'                => 'Parent Sets Category',
		'parent_item_colon'          => 'Parent Sets Category:',
		'edit_item'                  => 'Edit Sets Category',
		'update_item'                => 'Update Sets Category',
		'add_new_item'               => 'Add New Sets Category',
		'new_item_name'              => 'New Sets Category',
		'separate_items_with_commas' => 'Separate Sets Categories with commas',
		'add_or_remove_items'        => 'Add or remove Sets Categories',
		'choose_from_most_used'      => 'Choose from the most used Sets Categories',
		'menu_name'                  => 'Inclusion Exclusion Set Categories',
	];

	// Prepare args for registering taxonomy.
	$args = [
		'labels'            => $labels,
		'public'            => false,
		'show_in_nav_menus' => false,
		'show_ui'           => true,
		'show_in_menu'      => false,
		'show_tagcloud'     => false,
		'show_admin_column' => true,
		'hierarchical'      => true,
		'rewrite'           => false,
		'query_var'         => true,
		'capabilities'      => [],
		'show_in_rest'      => true,
	];

	// Register taxonomy.
	register_taxonomy( INCLUSION_EXCLUSION_CATEGORY, (array) apply_filters( 'qe_inclusion_exclusion_category_taxonomy_post_types', [] ), $args );
}

/**
 * Set taxonomy menu position.
 *
 * @return void
 */
function set_taxonomy_menu_position(): void {
	// Add taxonomy page under CPT.
	add_submenu_page(
		'edit.php?post_type=qrk_itinerary',
		'Inclusion Exclusion Set Categories',
		'Inclusion Exclusion Set Categories',
		'manage_categories',
		'edit-tags.php?taxonomy=' . INCLUSION_EXCLUSION_CATEGORY . '&post_type=' . POST_TYPE,
	);
}

/**
 * Opt into stuff.
 *
 * @param string[] $post_types Post types.
 *
 * @return string[]
 */
function opt_in( array $post_types = [] ): array {
	// Append Inclusion Set post type for taxonomy.
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
	// Get post type.
	$post_type = get_post_type( $post_id );

	// Check for post type.
	if ( POST_TYPE !== $post_type ) {
		return;
	}

	// Clear cache for this post.
	wp_cache_delete( CACHE_KEY . "_$post_id", CACHE_GROUP );

	// Trigger action to clear cache for this post.
	do_action( 'qe_inclusion_sets_post_cache_busted', $post_id );
}

/**
 * Get an Inclusion Set.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
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
			'post_meta'       => [],
			'post_taxonomies' => [],
		];
	}

	// Build data.
	$data = [
		'post'            => $post,
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
 * Translate meta keys.
 *
 * @param array<string, string> $meta_keys Meta keys.
 *
 * @return array<string, string|string[]>
 */
function translate_meta_keys( array $meta_keys = [] ): array {
	// Meta keys for translation.
	$extra_keys = [
		'display_title' => 'string',
		'set_\d+_item'  => 'string',
	];

	// Return meta keys to be translated.
	return array_merge( $meta_keys, $extra_keys );
}
