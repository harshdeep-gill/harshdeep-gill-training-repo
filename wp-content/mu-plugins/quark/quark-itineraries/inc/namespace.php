<?php
/**
 * Namespace functions.
 *
 * @package quark-itineraries
 */

namespace Quark\Itineraries;

use WP_Post;
use WP_Term;

use function Quark\Core\format_price;
use function Quark\InclusionSets\get as inclusion_sets_get;
use function Quark\PolicyPages\get as get_policy_page_post;
use function Quark\Brochures\get as get_brochure;
use function Quark\Departures\bust_post_cache as bust_departure_post_cache;
use function Quark\Expeditions\bust_post_cache as bust_expedition_post_cache;
use function Quark\Expeditions\get as get_expedition;
use function Quark\ItineraryDays\get as get_itinerary_day;
use function Quark\Leads\get_request_a_quote_url;
use function Quark\Localization\get_currencies;
use function Quark\Localization\get_current_currency;
use function Quark\Ships\get as get_ship;
use function Quark\Softrip\Departures\get_departures_by_itinerary;
use function Quark\Softrip\Departures\get_lowest_price as get_departure_lowest_price;
use function Quark\Softrip\Itineraries\get_related_ships;

use function Travelopia\Multilingual\get_post_translations;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Localization\DEFAULT_CURRENCY;
use const Quark\StaffMembers\SEASON_TAXONOMY;

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

	// Post cache bust. Assigning non-standard priority to avoid race conditions with ACF.
	add_action( 'save_post', __NAMESPACE__ . '\\bust_post_cache', 11 );

	// Update related expedition on itineraries save. Assigning higher priority to run before any other cache bust.
	add_action( 'save_post', __NAMESPACE__ . '\\update_related_expedition_on_itineraries_save', 1 );
	add_action( 'qe_departure_post_cache_busted', __NAMESPACE__ . '\\bust_lowest_price_cache_by_departure' );

	// Add meta keys to be translated while content sync.
	add_filter( 'qrk_translation_meta_keys', __NAMESPACE__ . '\\translate_meta_keys' );

	// Admin stuff.
	if ( is_admin() ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/itineraries.php';
		require_once __DIR__ . '/../custom-fields/tax-types.php';
		require_once __DIR__ . '/../custom-fields/departure-locations.php';
	}
}

/**
 * Update related itineraries meta of Expedition.
 *
 * @param int $post_id Post ID.
 *
 * @return void
 */
function update_related_expedition_on_itineraries_save( int $post_id = 0 ): void {
	// Bail if post ID is not provided.
	if ( empty( $post_id ) ) {
		return;
	}

	// Avoid running on auto-save, bulk edit, or during an Ajax request.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Ensure this only runs for the intended post type.
	if ( get_post_type( $post_id ) !== POST_TYPE ) {
		return;
	}

	// Meta key that we are monitoring in Post A.
	$meta_key = 'related_expedition';

	// Get the old meta value before the update.
	$old_expedition_post_id = absint( get_post_meta( $post_id, $meta_key, true ) );

	// Check if there is a new value being set in the post request.
	$new_expedition_post_id = ! empty( $_POST['acf'] ) && ! empty( $_POST['acf']['field_65f2dab2046df'] ) ? absint( sanitize_text_field( $_POST['acf']['field_65f2dab2046df'] ) ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing

	// Skip if both value are same.
	if ( $old_expedition_post_id === $new_expedition_post_id ) {
		return;
	}

	// If the old meta value existed and has been updated in the new data.
	// Let's remove it from the old expedition post.
	if ( ! empty( $old_expedition_post_id ) ) {
		// Get Expedition post meta 'related_itineraries' from old meta value.
		$related_itineraries = get_post_meta( $old_expedition_post_id, 'related_itineraries', true );

		// Check if related_itineraries is not empty.
		if ( ! empty( $related_itineraries ) && is_array( $related_itineraries ) ) {
			// Remove the current itinerary from the related_itineraries.
			$related_itineraries = array_diff( $related_itineraries, [ $post_id ] );

			// Update the expedition post meta 'related_itineraries'.
			update_post_meta( $old_expedition_post_id, 'related_itineraries', array_unique( $related_itineraries ) );

			// Bust cache.
			bust_expedition_post_cache( $old_expedition_post_id );
		}
	}

	// Add relation to new Expedition.
	if ( ! empty( $new_expedition_post_id ) ) {
		// Get Expedition post meta 'related_itineraries' from new meta value.
		$related_itineraries = get_post_meta( $new_expedition_post_id, 'related_itineraries', true );

		// Check if related_itineraries is not empty.
		if ( ! empty( $related_itineraries ) && is_array( $related_itineraries ) ) {
			// Add the current itinerary to the related_itineraries.
			$related_itineraries[] = $post_id;

			// Update the expedition post meta 'related_itineraries'.
			update_post_meta( $new_expedition_post_id, 'related_itineraries', array_unique( $related_itineraries ) );
		} else {
			// Update the expedition post meta 'related_itineraries'.
			update_post_meta( $new_expedition_post_id, 'related_itineraries', [ $post_id ] );
		}

		// Bust cache.
		bust_expedition_post_cache( $new_expedition_post_id );
	}

	/**
	 * Update related expedition on departure posts.
	 * Departures are children of itineraries.
	 */

	// Get departures of this itinerary.
	$departure_post_ids = get_children(
		[
			'post_parent'            => $post_id,
			'post_type'              => DEPARTURE_POST_TYPE,
			'post_status'            => [ 'publish', 'draft' ],
			'posts_per_page'         => -1,
			'fields'                 => 'ids',
			'orderby'                => 'ID',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'no_found_rows'          => true,
		],
		ARRAY_N
	);

	// Validate departure post IDs.
	$departure_post_ids = array_map( 'absint', $departure_post_ids );

	// Loop through each departure.
	foreach ( $departure_post_ids as $departure_post_id ) {
		// Update related expedition on departure.
		if ( ! empty( $new_expedition_post_id ) ) {
			// Update related expedition.
			update_post_meta( $departure_post_id, 'related_expedition', $new_expedition_post_id );
		} else {
			// Remove related expedition.
			delete_post_meta( $departure_post_id, 'related_expedition' );
		}

		// Bust cache.
		bust_departure_post_cache( $departure_post_id );
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
	// Get post type.
	$post_type = get_post_type( $post_id );

	// Check for post type.
	if ( POST_TYPE !== $post_type ) {
		return;
	}

	// Clear cache for this post.
	wp_cache_delete( CACHE_KEY . "_$post_id", CACHE_GROUP );

	// Bust lowest price cache.
	bust_lowest_price_cache( $post_id );

	// Trigger action to clear cache for this post.
	do_action( 'qe_itinerary_post_cache_busted', $post_id );
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
 * Get Season.
 *
 * @param int $post_id Post ID.
 *
 * @return array{}|array{
 *     term_id: int,
 *     name: string,
 *     slug: string,
 *     term_group: int,
 *     term_taxonomy_id: int,
 *     taxonomy: string,
 *     description: string,
 *     parent: int,
 * } Season data.
 */
function get_season( int $post_id = 0 ): array {
	// Get post ID.
	$post = get( $post_id );

	// If post not found then return empty array.
	if ( ! $post['post'] instanceof WP_Post ) {
		return [];
	}

	// If there is no season, return empty array.
	if (
		empty( $post['post_taxonomies'][ SEASON_TAXONOMY ] ) ||
		! is_array( $post['post_taxonomies'][ SEASON_TAXONOMY ] ) ||
		empty( $post['post_taxonomies'][ SEASON_TAXONOMY ][0] ) ||
		empty( $post['post_taxonomies'][ SEASON_TAXONOMY ][0]['term_id'] )
	) {
		return [];
	}

	// Return season data.
	return $post['post_taxonomies'][ SEASON_TAXONOMY ][0];
}

/**
 * Prepare details for itinerary tabs.
 *
 * @param int $expedition_id Expedition post ID.
 *
 * @return array<string|int, mixed>
 */
function get_details_tabs_data( int $expedition_id = 0 ): array {
	// Validate expedition ID.
	if ( empty( $expedition_id ) ) {
		return [];
	}

	// Get expedition.
	$expedition = get_expedition( $expedition_id );

	// Validate expedition.
	if ( empty( $expedition['post'] ) || ! $expedition['post'] instanceof WP_Post ) {
		return [];
	}

	// Check if the expedition meta is empty.
	if ( empty( $expedition['post_meta']['related_itineraries'] ) ) {
		return [];
	}

	// Get the itineraries.
	$itineraries = $expedition['post_meta']['related_itineraries'];

	// Check if the itineraries is an array.
	if ( ! is_array( $itineraries ) ) {
		return [];
	}

	// Build the component attributes.
	$details = [];

	// Loop through the itineraries.
	foreach ( $itineraries as $index => $itinerary ) {
		// Get the itinerary.
		$itinerary = get( $itinerary );

		// Check if the itinerary is empty.
		if ( ! $itinerary['post'] instanceof WP_Post ) {
			continue;
		}

		// Initialize variables for the component attributes.
		$tab_id             = sprintf( 'tab-%d', absint( $index ) + 1 );
		$tab_title          = '';
		$tab_subtitle       = '';
		$duration           = '';
		$tab_content_header = '';
		$departing_from     = '';
		$itinerary_days     = [];
		$brochure           = '';
		$ships              = [];

		// Prepare the tab title.
		if ( ! empty( $itinerary['post_meta']['duration_in_days'] ) ) {
			$tab_title = sprintf( '%d %s', absint( $itinerary['post_meta']['duration_in_days'] ), _n( 'Day', 'Days', absint( $itinerary['post_meta']['duration_in_days'] ), 'qrk' ) );
			$duration  = sprintf( '%d %s', absint( $itinerary['post_meta']['duration_in_days'] ), _n( 'day', 'days', absint( $itinerary['post_meta']['duration_in_days'] ), 'qrk' ) );
		}

		// Prepare the tab subtitle.
		if ( ! empty( $itinerary['post_meta']['start_location'] ) ) {
			$start_location = get_term_by( 'id', absint( $itinerary['post_meta']['start_location'] ), DEPARTURE_LOCATION_TAXONOMY );

			// Check if the start location is not empty.
			if ( $start_location instanceof WP_Term ) {
				// Set the departing from.
				$departing_from = $start_location->name;

				// Translators: %s is the start location name.
				$tab_subtitle = sprintf( __( 'From %s', 'qrk' ), $start_location->name );
			}
		}

		// Prepare the itinerary days accordion content.
		if ( ! empty( $itinerary['post_meta']['itinerary_days'] ) && is_array( $itinerary['post_meta']['itinerary_days'] ) ) {
			foreach ( $itinerary['post_meta']['itinerary_days'] as $itinerary_day ) {
				// Get the itinerary day.
				$itinerary_day = get_itinerary_day( $itinerary_day );

				// Check if the itinerary day is empty.
				if ( ! $itinerary_day['post'] instanceof WP_Post ) {
					continue;
				}

				// Append the itinerary day with the title and content.
				$itinerary_days[] = [
					'title'   => format_itinerary_day_title( $itinerary_day['post']->ID ),
					'content' => $itinerary_day['post']->post_content,
				];
			}
		}

		// Check if the itinerary has a brochure.
		if ( ! empty( $itinerary['post_meta']['brochure'] ) ) {
			$_brochure = get_brochure( absint( $itinerary['post_meta']['brochure'] ) );

			// Check if the brochure pdf is not empty.
			if ( ! empty( $_brochure['post_meta']['brochure_pdf'] ) ) {
				$brochure = wp_get_attachment_url( absint( $_brochure['post_meta']['brochure_pdf'] ) );
			} elseif ( ! empty( $_brochure['post_meta']['external_url'] ) ) {
				$brochure = $_brochure['post_meta']['external_url'];
			}
		}

		// Currency.
		$currency = get_current_currency();

		// Get the itinerary lowest price.
		$price = format_price( get_lowest_price( $itinerary['post']->ID, $currency )['discounted'], $currency );

		// Translators: %s is the lowest price.
		$price = ! empty( $price ) ? sprintf( __( '%s per person', 'qrk' ), $price ) : '';

		// Get the itinerary ships.
		$ship_post_ids = get_related_ships( $itinerary['post']->ID );

		// Loop through the ships.
		foreach ( $ship_post_ids as $ship_post_id ) {
			// Get ship.
			$ship = get_ship( $ship_post_id );

			// Check if the ship post is empty.
			if ( is_array( $ship ) && ! empty( $ship['post'] ) && $ship['post'] instanceof WP_Post ) {
				// Append the ship to the ships list.
				$ships[] = [
					'name' => $ship['post']->post_title,
					'link' => $ship['permalink'],
				];
			}
		}

		// Append the tab subtitle.
		if ( ! empty( $tab_subtitle ) ) {
			$tab_content_header .= $tab_subtitle;
		}

		// Append the duration.
		if ( ! empty( $duration ) ) {
			$tab_content_header .= ! empty( $tab_content_header ) ? ', ' : '';
			$tab_content_header .= $duration;
		}

		// Append the ship names.
		if ( is_array( $ships ) ) {
			$on = array_column( $ships, 'name' );

			// Check if the ship names are not empty.
			if ( ! empty( $on ) ) {
				$tab_content_header .= ! empty( $tab_content_header ) ? ', on ' : '';
				$tab_content_header .= implode( ', ', $on );
			}
		}

		// Get itinerary ships.
		$season = get_season( $itinerary['post']->ID );

		// Check if the season is empty.
		if ( empty( $season ) ) {
			continue;
		}

		// Active tab for seasons tabs.
		if ( ! isset( $details['active_tab'] ) || absint( $season['slug'] ) < absint( $details['active_tab'] ) ) {
			$details['active_tab'] = $season['slug'];
		}

		// Seasons tab data.
		$details['itinerary_groups'][ $season['slug'] ]['tab_id'] = $season['slug'];

		// To show next year - show only if root destination term has meta show_next_year set to true.
		$to_show_next_year = false;

		// Get destination terms of expedition.
		$destinations = $expedition['post_taxonomies'][ DESTINATION_TAXONOMY ] ?? [];

		// Check if destinations is not empty.
		if ( is_array( $destinations ) && ! empty( $destinations ) ) {
			// Select destination whose parent is empty.
			$destinations = array_filter(
				$destinations,
				fn( $destination ) => empty( $destination['parent'] )
			);

			// Check if destinations is not empty.
			if ( ! empty( $destinations ) ) {
				// Select first.
				$destination = reset( $destinations );

				// Check if destination is not empty.
				if ( ! empty( $destination ) && is_array( $destination ) && ! empty( $destination['term_id'] ) ) {
					// Get term meta.
					$term_meta = get_term_meta( $destination['term_id'], 'show_next_year', true );

					// Check if term meta is not empty.
					if ( ! empty( $term_meta ) ) {
						$to_show_next_year = true;
					}
				}
			}
		}

		// Set tab title.
		if ( $to_show_next_year ) {
			/* Translators: %d.%d is the season name. */
			$details['itinerary_groups'][ $season['slug'] ]['tab_title'] = sprintf( __( '%1$d.%2$d Season', 'qrk' ), $season['name'], absint( substr( $season['name'], -2 ) ) + 1 );
		} else {
			/* Translators: %d is the season name */
			$details['itinerary_groups'][ $season['slug'] ]['tab_title'] = sprintf( __( '%d Season', 'qrk' ), $season['name'] );
		}

		// Active tab for itinerary tabs.
		if ( ! isset( $details['itinerary_groups'][ $season['slug'] ]['active_tab'] ) ) {
			$details['itinerary_groups'][ $season['slug'] ]['active_tab'] = $tab_id;
		}

		// Append the itinerary to the component attributes.
		$details['itinerary_groups'][ $season['slug'] ]['itineraries'][] = [
			'tab_id'              => $tab_id,
			'tab_title'           => $tab_title,
			'tab_subtitle'        => $tab_subtitle,
			'tab_content_header'  => $tab_content_header,
			'duration'            => $duration,
			'departing_from'      => $departing_from,
			'itinerary_days'      => $itinerary_days,
			'map'                 => $itinerary['post_meta']['map'] ?? 0,
			'price'               => $price,
			'brochure'            => $brochure,
			'ships'               => $ships,
			'request_a_quote_url' => get_request_a_quote_url( 0, $expedition_id ),
		];
	}

	// Sort the itinerary groups.
	if ( isset( $details['itinerary_groups'] ) ) {
		ksort( $details['itinerary_groups'] );
	}

	// Return the component attributes.
	return $details;
}

/**
 * Prepare the itinerary day title for display.
 *
 * @param int $itinerary_day The itinerary day ID.
 *
 * @return string The itinerary day title.
 */
function format_itinerary_day_title( int $itinerary_day = 0 ): string {
	// Get the itinerary day.
	$itinerary_day = get_itinerary_day( $itinerary_day );

	// Check if the itinerary day is empty.
	if ( ! $itinerary_day['post'] instanceof WP_Post ) {
		return '';
	}

	// Check if the itinerary day has a title.
	if ( empty( $itinerary_day['post_meta']['day_title'] ) ) {
		return '';
	}

	// Check if the itinerary day is empty.
	if ( empty( $itinerary_day['post_meta']['day_number_from'] ) || empty( $itinerary_day['post_meta']['day_number_to'] ) ) {
		return strval( $itinerary_day['post_meta']['day_title'] );
	}

	// Example: Day 1: Day Title.
	if ( absint( $itinerary_day['post_meta']['day_number_from'] ) === absint( $itinerary_day['post_meta']['day_number_to'] ) ) {
		return sprintf(
			/* Translators: %s: Day number from, %s: Day title */
			__( 'Day %1$s: %2$s', 'qrk' ),
			$itinerary_day['post_meta']['day_number_from'],
			strval( $itinerary_day['post_meta']['day_title'] )
		);
	}

	// Return: Day 1 & 2: Day Title.
	if ( absint( $itinerary_day['post_meta']['day_number_from'] ) + 1 === absint( $itinerary_day['post_meta']['day_number_to'] ) ) {
		return sprintf(
			/* Translators: %s: Day number from, %s: Day number to, %s: Day title */
			__( 'Day %1$s & %2$s: %3$s', 'qrk' ),
			$itinerary_day['post_meta']['day_number_from'],
			$itinerary_day['post_meta']['day_number_to'],
			strval( $itinerary_day['post_meta']['day_title'] )
		);
	}

	// Return: Day 3 to 5: Day Title.
	if ( absint( $itinerary_day['post_meta']['day_number_from'] ) + 1 < absint( $itinerary_day['post_meta']['day_number_to'] ) ) {
		return sprintf(
			/* Translators: %s: Day number from, %s: Day number to, %s: Day title */
			__( 'Day %1$s to %2$s: %3$s', 'qrk' ),
			$itinerary_day['post_meta']['day_number_from'],
			$itinerary_day['post_meta']['day_number_to'],
			strval( $itinerary_day['post_meta']['day_title'] )
		);
	}

	// Return: the day title.
	return strval( $itinerary_day['post_meta']['day_title'] );
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

	// Check for $itinerary has meta_data.
	if ( ! is_array( $itinerary['post_meta'] ) || empty( $itinerary['post_meta']['start_location'] ) ) {
		return $starting_from_location;
	}

	// Get starting from location.
	$location_term = get_term_by( 'id', absint( $itinerary['post_meta']['start_location'] ), DEPARTURE_LOCATION_TAXONOMY );

	// Check valid term.
	if ( $location_term instanceof WP_Term ) {
		$starting_from_location = $location_term->name;
	}

	// Return starting from location.
	return $starting_from_location;
}

/**
 * Get Ending To Location for Itinerary.
 *
 * @param int $post_id Post ID.
 *
 * @return string The ending to location.
 */
function get_end_location( int $post_id = 0 ): string {
	// Get post.
	$itinerary          = get( $post_id );
	$ending_to_location = '';

	// Check for $itinerary has meta_data.
	if ( ! is_array( $itinerary['post_meta'] ) || empty( $itinerary['post_meta']['end_location'] ) ) {
		return $ending_to_location;
	}

	// Get starting from location.
	$location_term = get_term_by( 'id', absint( $itinerary['post_meta']['end_location'] ), DEPARTURE_LOCATION_TAXONOMY );

	// Check valid term.
	if ( $location_term instanceof WP_Term ) {
		$ending_to_location = $location_term->name;
	}

	// Return starting from location.
	return $ending_to_location;
}

/**
 * Get Mandatory Transfer Price for Itinerary.
 *
 * @param int    $post_id Post ID.
 * @param string $currency Currency code.
 *
 * @return int Mandatory transfer price.
 */
function get_mandatory_transfer_price( int $post_id = 0, string $currency = DEFAULT_CURRENCY ): int {
	// get Itinerary.
	$itinerary = get( $post_id );

	// Validate.
	if ( ! $itinerary['post'] instanceof WP_Post ) {
		return 0;
	}

	// Get meta key.
	$meta_key = sprintf( 'mandatory_transfer_price_%s', strtolower( $currency ) );

	// Check for meta key exists.
	if ( empty( $itinerary['post_meta'][ $meta_key ] ) || ! is_numeric( $itinerary['post_meta'][ $meta_key ] ) ) {
		return 0;
	}

	// Get mandatory transfer price.
	return absint( $itinerary['post_meta'][ $meta_key ] );
}

/**
 * Get Supplemental Price for Itinerary.
 *
 * @param int    $post_id Post ID.
 * @param string $currency Currency code.
 *
 * @return int Supplemental price.
 */
function get_supplemental_price( int $post_id = 0, string $currency = DEFAULT_CURRENCY ): int {
	// get Itinerary.
	$itinerary = get( $post_id );

	// Validate.
	if ( ! $itinerary['post'] instanceof WP_Post ) {
		return 0;
	}

	// Get meta key.
	$meta_key = sprintf( 'supplement_price_%s', strtolower( $currency ) );

	// Check for meta key exists.
	if ( empty( $itinerary['post_meta'][ $meta_key ] ) || ! is_numeric( $itinerary['post_meta'][ $meta_key ] ) ) {
		return 0;
	}

	// Get supplemental price.
	return absint( $itinerary['post_meta'][ $meta_key ] );
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
 *     price: int,
 *     formatted_price: string,
 * } Included transfer package.
 */
function get_included_transfer_package_details( int $post_id = 0, string $currency = DEFAULT_CURRENCY ): array {
	// get Itinerary.
	$itinerary = get( $post_id );
	$details   = [
		'title'                => '',
		'sets'                 => [],
		'price'                => 0,
		'formatted_price'      => '0',
		'offer_inclusion_text' => '',
	];

	// Validate.
	if ( ! $itinerary['post'] instanceof WP_Post ) {
		return $details;
	}

	// Get included transfer package.
	$details['price'] = get_mandatory_transfer_price( $post_id, $currency );

	// Bail if empty price.
	if ( empty( $details['price'] ) ) {
		return $details;
	}

	// Format price.
	$details['formatted_price'] = format_price( $details['price'], $currency );

	// Offer Inclusion Text.
	if ( ! empty( $itinerary['post_meta']['offer_inclusion_text'] ) ) {
		$details['offer_inclusion_text'] = $itinerary['post_meta']['offer_inclusion_text'];
	}

	// Get included transfer package.
	$transfer_package_id = $itinerary['post_meta']['mandatory_transfer_package_inclusion'] ?? 0;

	// Check for transfer package.
	if ( empty( $transfer_package_id ) ) {
		return $details;
	}

	// Get Inclusion Set.
	$inclusion_set = inclusion_sets_get( absint( $transfer_package_id ) );

	// Verify post_meta is not empty.
	if ( ! is_array( $inclusion_set['post_meta'] ) ) {
		return $details;
	}

	// Get Display Title.
	$details['title'] = ! empty( $inclusion_set['post_meta']['display_title'] ) ? strval( $inclusion_set['post_meta']['display_title'] ) : __( 'Includes', 'qrk' );

	// Check for Inclusion Set.
	if ( empty( $inclusion_set['post_meta']['set'] ) ) {
		// Return details.
		return $details;
	}

	// Loop through set items.
	for ( $i = 0; $i < $inclusion_set['post_meta']['set']; $i++ ) {
		$details['sets'][] = $inclusion_set['post_meta'][ 'set_' . $i . '_item' ] ? strval( $inclusion_set['post_meta'][ 'set_' . $i . '_item' ] ) : '';
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
	if ( ! is_array( $itinerary['post_meta'] ) ) {
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
	if ( ! is_array( $policy_post['post_meta'] ) ) {
		return $details;
	}

	// Return policy banner details.
	return [
		'title'       => ! empty( $policy_post['post_meta']['alternate_title'] ) ? strval( $policy_post['post_meta']['alternate_title'] ) : '',
		'icon_id'     => ! empty( $policy_post['post_meta']['marketing_option_icon'] ) ? absint( $policy_post['post_meta']['marketing_option_icon'] ) : 0,
		'description' => ! empty( $policy_post['post_meta']['marketing_option_summary'] ) ? strval( $policy_post['post_meta']['marketing_option_summary'] ) : '',
		'permalink'   => strval( $policy_post['permalink'] ),
	];
}

/**
 * Get tax type details.
 *
 * @param int $post_id Post ID.
 *
 * @return array<int, array{
 *   id: int,
 *   name: string,
 *   description: string,
 *   rate: int,
 * }>
 */
function get_tax_type_details( int $post_id = 0 ): array {
	// Initialize tax types.
	$tax_types = [];

	// Get itinerary.
	$itinerary = get( $post_id );

	// Validate itinerary.
	if ( ! $itinerary['post'] instanceof WP_Post ) {
		return $tax_types;
	}

	// Validate taxonomies.
	if ( empty( $itinerary['post_taxonomies'] ) || empty( $itinerary['post_taxonomies'][ TAX_TYPE_TAXONOMY ] ) || ! is_array( $itinerary['post_taxonomies'][ TAX_TYPE_TAXONOMY ] ) ) {
		return $tax_types;
	}

	// Loop through taxonomies.
	foreach ( $itinerary['post_taxonomies'][ TAX_TYPE_TAXONOMY ] as $tax_type ) {
		$tax_type = [
			'id'          => absint( $tax_type['term_id'] ),
			'name'        => strval( $tax_type['name'] ),
			'description' => strval( $tax_type['description'] ),
			'rate'        => 0,
		];

		// Get rate from term meta.
		$rate = absint( get_term_meta( $tax_type['id'], 'rate', true ) );

		// Check for rate.
		if ( $rate ) {
			$tax_type['rate'] = $rate;
		}

		// Append tax type.
		$tax_types[] = $tax_type;
	}

	// Return tax types.
	return $tax_types;
}

/**
 * Get lowest price for itinerary.
 *
 * @param int    $post_id  Itinerary post ID.
 * @param string $currency Currency code.
 * @param bool   $force    Whether cached value should be ignored.
 *
 * @return array{
 *  original: int,
 *  discounted: int,
 * }
 */
function get_lowest_price( int $post_id = 0, string $currency = DEFAULT_CURRENCY, bool $force = false ): array {
	// Uppercase the currency code.
	$currency = strtoupper( $currency );

	// Setup default return values.
	$lowest_price = [
		'original'   => 0,
		'discounted' => 0,
	];

	// Return default values if no post ID.
	if ( empty( $post_id ) || ! in_array( $currency, get_currencies(), true ) ) {
		return $lowest_price;
	}

	// Cache key.
	$cache_key = CACHE_KEY . '_lowest_price_' . $post_id . '_' . $currency;

	// If not forced, check cache.
	if ( ! $force ) {
		$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

		// Check for cached value.
		if ( is_array( $cached_value ) && ! empty( $cached_value['original'] ) && ! empty( $cached_value['discounted'] ) ) {
			return $cached_value;
		}
	}

	// Get all departure posts for the itinerary.
	$departure_post_ids = get_departures_by_itinerary( $post_id );

	// Loop through each departure post.
	foreach ( $departure_post_ids as $departure_post_id ) {
		// Get the lowest price for the departure.
		$departure_price = get_departure_lowest_price( $departure_post_id, $currency );

		// If the discounted price is less than the current discounted price, update the discounted and original price.
		if ( empty( $lowest_price['discounted'] ) || $departure_price['discounted'] < $lowest_price['discounted'] ) {
			$lowest_price['discounted'] = $departure_price['discounted'];
			$lowest_price['original']   = $departure_price['original'];
		}
	}

	// Set cache.
	wp_cache_set( $cache_key, $lowest_price, CACHE_GROUP );

	// Return the lowest price.
	return $lowest_price;
}

/**
 * Bust cache for lowest price.
 *
 * @param int $post_id  Itinerary post ID.
 *
 * @return void
 */
function bust_lowest_price_cache( int $post_id = 0 ): void {
	// Bail if no post ID.
	if ( empty( $post_id ) ) {
		return;
	}

	// Currencies.
	$currencies = get_currencies();

	// Loop through each currency.
	foreach ( $currencies as $currency ) {
		// Bust cache for lowest price.
		wp_cache_delete( CACHE_KEY . '_lowest_price_' . $post_id . '_' . $currency, CACHE_GROUP );
	}
}

/**
 * Bust cache for lowest price by departure.
 *
 * @param int $departure_id Departure post ID.
 *
 * @return void
 */
function bust_lowest_price_cache_by_departure( int $departure_id = 0 ): void {
	// Bail if no departure ID.
	if ( empty( $departure_id ) ) {
		return;
	}

	// Get itinerary.
	$itinerary_post_id = get_post_meta( $departure_id, 'itinerary', true );

	// Validate itinerary.
	if ( empty( $itinerary_post_id ) ) {
		return;
	}

	// Convert to integer.
	$itinerary_post_id = absint( $itinerary_post_id );

	// Bust cache for lowest price.
	bust_lowest_price_cache( $itinerary_post_id );
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
		'boilerplate'                          => 'string',
		'related_expedition'                   => 'post',
		'start_location'                       => 'taxonomy',
		'end_location'                         => 'taxonomy',
		'embarkation_port'                     => 'post',
		'disembarkation_port'                  => 'post',
		'brochure'                             => 'post',
		'map'                                  => 'attachment',
		'inclusions'                           => 'post',
		'exclusions'                           => 'post',
		'itinerary_days'                       => __NAMESPACE__ . '\\translate_meta_key',
		'offer_inclusion_text'                 => 'string',
		'mandatory_transfer_package_inclusion' => 'post',
		'mandatory_transfer_package_exclusion' => 'post',
		'tnc_cancellation_policy'              => 'post',
		'tnc_terms_and_conditions'             => 'post',
	];

	// Return meta keys to be translated.
	return array_merge( $meta_keys, $extra_keys );
}

/**
 * Callable to translate a meta value by meta key.
 *
 * @param string $meta_key            Meta key name.
 * @param string $meta_value          Meta key value.
 * @param int    $source_site_id      Source site ID.
 * @param int    $destination_site_id Destination site ID.
 *
 * @return string Translated value.
 */
function translate_meta_key( string $meta_key = '', string $meta_value = '', int $source_site_id = 0, int $destination_site_id = 0 ): string {
	// Bail if required data is not available.
	if ( empty( $meta_key ) || empty( $meta_value ) || empty( $source_site_id ) || empty( $destination_site_id ) ) {
		return $meta_value;
	}

	// Check for itinerary days meta key.
	if ( 'itinerary_days' === $meta_key ) {
		// Get translated deck ID.
		$itinerary_day = get_post_translations(
			absint( $meta_value ),
			$source_site_id
		);

		// Loop through translated posts.
		foreach ( $itinerary_day as $post ) {
			if ( $post['site_id'] === $destination_site_id ) {
				// Update meta value.
				$meta_value = $post['post_id'];
			}
		}
	}

	// Return meta value.
	return strval( $meta_value );
}
