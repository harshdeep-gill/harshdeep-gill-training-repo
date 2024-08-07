<?php
/**
 * Namespace functions.
 *
 * @package quark-itineraries
 */

namespace Quark\Itineraries;

use WP_Post;
use WP_Term;

use function Quark\InclusionSets\get as inclusion_sets_get;
use function Quark\PolicyPages\get as get_policy_page_post;

use const Quark\StaffMembers\SEASON_TAXONOMY as SEASON_TAXONOMY;

const POST_TYPE                   = 'qrk_itinerary';
const DEPARTURE_LOCATION_TAXONOMY = 'qrk_departure_location';
const TAX_TYPE_TAXONOMY           = 'qrk_tax_type';
const CACHE_KEY                   = POST_TYPE;
const CACHE_GROUP                 = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type and taxonomies.
	add_action( 'init', __NAMESPACE__ . '\\register_itinerary_post_type' );
	add_action( 'init', __NAMESPACE__ . '\\register_departure_location_taxonomy' );
	add_action( 'init', __NAMESPACE__ . '\\register_tax_type_taxonomy' );

	// Opt into stuff.
	add_filter( 'qe_departure_location_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_tax_types_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_season_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );

	// Other hooks.
	add_action( 'save_post_' . POST_TYPE, __NAMESPACE__ . '\\bust_post_cache' );

	// Bust cache on term update.
	add_action( 'set_object_terms', __NAMESPACE__ . '\\bust_post_cache_on_term_assign', 10, 6 );

	// Admin stuff.
	if ( is_admin() ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/itineraries.php';
	}
}

/**
 * Register Itinerary post type.
 *
 * @return void
 */
function register_itinerary_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Itineraries',
			'singular_name'      => 'Itinerary',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Itinerary',
			'edit_item'          => 'Edit Itinerary',
			'new_item'           => 'New Itinerary',
			'view_item'          => 'View Itinerary',
			'search_items'       => 'Search Itineraries',
			'not_found'          => 'No Itineraries found',
			'not_found_in_trash' => 'No Itineraries found in Trash',
			'parent_item_colon'  => 'Parent Itinerary:',
			'menu_name'          => 'Itineraries',
		],
		'public'              => false,
		'show_in_rest'        => false,
		'menu_icon'           => 'dashicons-list-view',
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
 * Register Departure Location taxonomy.
 *
 * @return void
 */
function register_departure_location_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Departure & Arrival Locations',
		'singular_name'              => 'Departure & Arrival Location',
		'search_items'               => 'Search Departure & Arrival Locations',
		'popular_items'              => 'Popular Departure & Arrival Locations',
		'all_items'                  => 'All Departure & Arrival Locations',
		'parent_item'                => 'Parent Departure & Arrival Location',
		'parent_item_colon'          => 'Parent Departure & Arrival Location:',
		'edit_item'                  => 'Edit Departure & Arrival Location',
		'update_item'                => 'Update Departure & Arrival Location',
		'add_new_item'               => 'Add New Departure & Arrival Location',
		'new_item_name'              => 'New Departure & Arrival Location',
		'separate_items_with_commas' => 'Separate Departure & Arrival Locations with commas',
		'add_or_remove_items'        => 'Add or remove Departure & Arrival Locations',
		'choose_from_most_used'      => 'Choose from the most used Departure & Arrival Locations',
		'menu_name'                  => 'Departure & Arrival Locations',
	];

	// Prepare args for registering taxonomy.
	$args = [
		'labels'            => $labels,
		'public'            => false,
		'show_in_nav_menus' => false,
		'show_ui'           => true,
		'meta_box_cb'       => false,
		'show_tagcloud'     => false,
		'show_admin_column' => true,
		'hierarchical'      => true,
		'rewrite'           => false,
		'query_var'         => true,
		'capabilities'      => [],
		'show_in_rest'      => true,
	];

	// Register taxonomy.
	register_taxonomy( DEPARTURE_LOCATION_TAXONOMY, (array) apply_filters( 'qe_departure_location_taxonomy_post_types', [] ), $args );
}

/**
 * Register Tax Type taxonomy.
 *
 * @return void
 */
function register_tax_type_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Tax Types',
		'singular_name'              => 'Tax Type',
		'search_items'               => 'Search Tax Types',
		'popular_items'              => 'Popular Tax Types',
		'all_items'                  => 'All Tax Types',
		'parent_item'                => 'Parent Tax Type',
		'parent_item_colon'          => 'Parent Tax Type:',
		'edit_item'                  => 'Edit Tax Type',
		'update_item'                => 'Update Tax Type',
		'add_new_item'               => 'Add New Tax Type',
		'new_item_name'              => 'New Tax Type',
		'separate_items_with_commas' => 'Separate Tax Types with commas',
		'add_or_remove_items'        => 'Add or remove Tax Types',
		'choose_from_most_used'      => 'Choose from the most used Tax Types',
		'menu_name'                  => 'Tax Types',
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
	register_taxonomy( TAX_TYPE_TAXONOMY, (array) apply_filters( 'qe_tax_types_taxonomy_post_types', [] ), $args );
}

/**
 * Opt into stuff.
 *
 * @param string[] $post_types Post types.
 *
 * @return string[]
 */
function opt_in( array $post_types = [] ): array {
	// Append Itinerary post type for taxonomy.
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
	do_action( 'qe_itinerary_post_cache_busted', $post_id );
}

/**
 * Bust cache on term assign.
 *
 * @param int                    $object_id Object ID.
 * @param array{string|int}|null $terms     An array of object term IDs or slugs.
 * @param array{string|int}|null $tt_ids    An array of term taxonomy IDs.
 * @param string                 $taxonomy  Taxonomy slug.
 *
 * @return void
 */
function bust_post_cache_on_term_assign( int $object_id = 0, array $terms = null, array $tt_ids = null, string $taxonomy = '' ): void {
	// Check for taxonomy.
	if ( in_array( $taxonomy, [ DEPARTURE_LOCATION_TAXONOMY, TAX_TYPE_TAXONOMY, SEASON_TAXONOMY ], true ) ) {
		// Get post.
		$post = get( $object_id );

		// Check for post.
		if ( ! $post['post'] instanceof WP_Post || POST_TYPE !== $post['post']->post_type ) {
			return;
		}

		// Bust cache.
		bust_post_cache( $post['post']->ID );
	}
}

/**
 * Get an Itinerary.
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
 * Get Starting from Location for Itinerary.
 *
 * @param int $post_id Post ID.
 *
 * @return string The starting from location.
 */
function get_starting_from_location( int $post_id = 0 ): string {
	// Get post.
	$itinerary              = get( $post_id );
	$starting_from_location = '';

	// Check for valid itinerary.
	if ( ! $itinerary['post'] instanceof WP_Post ) {
		return $starting_from_location;
	}

	// Get starting from location.
	$starting_from_location_id = $itinerary['post_meta']['start_location'] ? absint( $itinerary['post_meta']['start_location'] ) : '';
	$location_term             = get_term_by( 'id', $starting_from_location_id, DEPARTURE_LOCATION_TAXONOMY );

	// Check valid term.
	if ( $location_term instanceof WP_Term ) {
		$starting_from_location = $location_term->name;
	}

	// Return starting from location.
	return $starting_from_location;
}

/**
 * Get Mandatory Transfer Price for Itinerary.
 *
 * @param int    $post_id Post ID.
 * @param string $currency Currency code.
 *
 * @return float Mandatory transfer price.
 */
function get_mandatory_transfer_price( int $post_id = 0, string $currency = 'USD' ): float {
	// get Itinerary.
	$itinerary = get( $post_id );

	// Validate.
	if ( ! $itinerary['post'] instanceof WP_Post ) {
		return 0;
	}

	// Get meta key.
	$meta_key = sprintf( 'mandatory_transfer_price_%s', strtolower( $currency ) );

	// Get mandatory transfer price.
	return floatval( strval( $itinerary['post_meta'][ $meta_key ] ) );
}

/**
 * Get Supplemental Price for Itinerary.
 *
 * @param int    $post_id Post ID.
 * @param string $currency Currency code.
 *
 * @return float Supplemental price.
 */
function get_supplemental_price( int $post_id = 0, string $currency = 'USD' ): float {
	// get Itinerary.
	$itinerary = get( $post_id );

	// Validate.
	if ( ! $itinerary['post'] instanceof WP_Post ) {
		return 0;
	}

	// Get meta key.
	$meta_key = sprintf( 'supplemental_price_%s', strtolower( $currency ) );

	// Get supplemental price.
	return floatval( strval( $itinerary['post_meta'][ $meta_key ] ) );
}

/**
 * Get Included transfer package for Itinerary.
 *
 * @param int    $post_id  Post ID.
 * @param string $currency Currency code.
 *
 * @return array{
 *     title: string,
 *     sets: string[],
 *     price: float,
 * } Included transfer package.
 */
function get_included_transfer_package_details( int $post_id = 0, string $currency = 'USD' ): array {
	// get Itinerary.
	$itinerary = get( $post_id );
	$details   = [
		'title' => '',
		'sets'  => [],
		'price' => 0,
	];

	// Validate.
	if ( ! $itinerary['post'] instanceof WP_Post ) {
		return $details;
	}

	// Get included transfer package.
	$details['price'] = get_mandatory_transfer_price( $post_id, $currency );

	// Get included transfer package.
	$transfer_package_id = $itinerary['post_meta']['mandatory_transfer_package_inclusion'] ?? 0;

	// Check for transfer package.
	if ( empty( $transfer_package_id ) ) {
		return $details;
	}

	// Get Inclusion Set.
	$inclusion_set = inclusion_sets_get( absint( $transfer_package_id ) );

	// Get Display Title.
	$details['title'] = $inclusion_set['post_meta']['display_title'] ? strval( $inclusion_set['post_meta']['display_title'] ) : __( 'Includes', 'qrk' );

	// Check for Inclusion Set.
	if ( empty( $inclusion_set['post_meta']['set'] ) ) {
		// Return details.
		return $details;
	}

	// Loop through set items.
	for ( $i = 0; $i < $inclusion_set['post_meta']['set']; $i++ ) {
		$details['sets'][] = $inclusion_set['post_meta'][ 'set_' . $i . '_item' ] ?? '';
	}

	// Return details.
	return $details;
}

/**
 * Get departure policy banner details.
 *
 * @param int $itinerary_id Itinerary Post ID.
 *
 * @return array{
 *   title: string,
 *   icon_id: int,
 *   description: string,
 *   permalink: string,
 * } Policy Banner Details.
 */
function get_policy_banner_details( int $itinerary_id = 0 ): array {
	// Get itinerary.
	$itinerary = get( $itinerary_id );
	$details   = [
		'title'       => '',
		'icon_id'     => 0,
		'description' => '',
		'permalink'   => '',
	];

	// Check post_meta is not empty.
	if ( ! $itinerary['post_meta'] ) {
		return $details;
	}

	// Get policy banner details from meta.
	$policy_banner_details = $itinerary['post_meta']['tnc_cancellation_policy'] ?? 0;

	// Check meta is empty.
	if ( ! $policy_banner_details ) {
		return $details;
	}

	// Get policy post.
	$policy_post = get_policy_page_post( absint( $policy_banner_details ) );

	// Check policy post meta is not empty.
	if ( empty( $policy_post['post_meta'] ) ) {
		return $details;
	}

	// Return policy banner details.
	return [
		'title'       => $policy_post['post_meta']['alternate_title'] ? strval( $policy_post['post_meta']['alternate_title'] ) : '',
		'icon_id'     => $policy_post['post_meta']['marketing_option_icon'] ? absint( $policy_post['post_meta']['marketing_option_icon'] ) : 0,
		'description' => $policy_post['post_meta']['marketing_option_summary'] ? strval( $policy_post['post_meta']['marketing_option_summary'] ) : '',
		'permalink'   => strval( $policy_post['permalink'] ),
	];
}
