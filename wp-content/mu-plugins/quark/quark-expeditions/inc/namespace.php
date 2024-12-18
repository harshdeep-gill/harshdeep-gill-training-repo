<?php
/**
 * Namespace functions.
 *
 * @package quark-expeditions
 */

namespace Quark\Expeditions;

use WP_Post;
use WP_Error;
use WP_Screen;
use WP_Term;
use WP_REST_Response;
use WP_Taxonomy;
use WP_REST_Request;

use function Quark\Itineraries\get as get_itinerary;
use function Quark\Itineraries\format_itinerary_day_title;
use function Quark\ItineraryDays\get as get_itinerary_day;
use function Quark\Departures\get as get_departure;
use function Quark\Core\format_price;
use function Quark\Localization\get_currencies;
use function Quark\Localization\get_current_currency;
use function Quark\Ships\get as get_ship;
use function Quark\Softrip\Departures\get_departures_by_itinerary;
use function Quark\Softrip\Itineraries\get_end_date;
use function Quark\Itineraries\get_lowest_price as get_itinerary_lowest_price;
use function Quark\Softrip\Itineraries\get_related_ships;
use function Quark\Softrip\Itineraries\get_start_date;

use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;

const POST_TYPE                    = 'qrk_expedition';
const DESTINATION_TAXONOMY         = 'qrk_destination';
const EXPEDITION_CATEGORY_TAXONOMY = 'qrk_expedition_category';
const EXPEDITION_TAG_TAXONOMY      = 'qrk_expedition_tag';
const EXCURSION_TAXONOMY           = 'qrk_excursion';
const CACHE_KEY                    = POST_TYPE;
const CACHE_GROUP                  = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type and taxonomies.
	add_action( 'init', __NAMESPACE__ . '\\register_expedition_post_type' );
	add_action( 'init', __NAMESPACE__ . '\\register_destination_taxonomy' );
	add_action( 'init', __NAMESPACE__ . '\\register_expedition_category_taxonomy' );
	add_action( 'init', __NAMESPACE__ . '\\register_expedition_tag_taxonomy' );
	add_action( 'init', __NAMESPACE__ . '\\register_excursion_taxonomy' );

	// Opt into stuff.
	add_filter( 'qe_adventure_options_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_destination_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_expedition_category_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_expedition_tag_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_excursion_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'rest_prepare_taxonomy', __NAMESPACE__ . '\\hide_excursion_metabox', 10, 3 );

	// Other hooks. Assigning non-standard priority to avoid race conditions with ACF.
	add_action( 'save_post', __NAMESPACE__ . '\\bust_post_cache', 11 );
	add_filter( 'travelopia_seo_structured_data_schema', __NAMESPACE__ . '\\seo_structured_data' );

	// Bust cache for details data.
	add_action( 'qe_expedition_post_cache_busted', __NAMESPACE__ . '\\bust_details_cache' );
	add_action( 'qe_itinerary_post_cache_busted', __NAMESPACE__ . '\\bust_details_cache_on_itinerary_update' );
	add_action( 'qe_departure_post_cache_busted', __NAMESPACE__ . '\\bust_details_cache_on_departure_update' );

	// Breadcrumbs.
	add_filter( 'travelopia_breadcrumbs_ancestors', __NAMESPACE__ . '\\breadcrumbs_ancestors' );

	// Related Itineraries Meta box.
	add_action( 'add_meta_boxes', __NAMESPACE__ . '\\add_related_itineraries_meta_box' );

	// Admin stuff.
	if ( is_admin() ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/expeditions.php';
		require_once __DIR__ . '/../custom-fields/destinations.php';
		require_once __DIR__ . '/../custom-fields/excursion.php';
	}
}

/**
 * Register Expedition post type.
 *
 * @return void
 */
function register_expedition_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Expeditions',
			'singular_name'      => 'Expedition',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Expedition',
			'edit_item'          => 'Edit Expedition',
			'new_item'           => 'New Expedition',
			'view_item'          => 'View Expedition',
			'search_items'       => 'Search Expeditions',
			'not_found'          => 'No Expeditions found',
			'not_found_in_trash' => 'No Expeditions found in Trash',
			'parent_item_colon'  => 'Parent Expedition:',
			'menu_name'          => 'Expeditions',
		],
		'public'              => true,
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-flag',
		'hierarchical'        => false,
		'supports'            => [
			'title',
			'editor',
			'revisions',
			'thumbnail',
			'excerpt',
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
			'slug'       => 'expeditions',
			'with_front' => false,
		],
		'template'            => [
			[
				'quark/expedition-hero',
				[],
			],
			[
				'quark/secondary-navigation',
				[],
				[
					[
						'quark/secondary-navigation-menu',
						[],
						[
							[
								'quark/secondary-navigation-item',
								[
									'title' => 'Overview',
									'url'   => [
										'url'  => 'overview',
										'text' => 'Overview',
									],
								],
							],
							[
								'quark/secondary-navigation-item',
								[
									'title' => 'Itineraries',
									'url'   => [
										'url'  => 'itineraries',
										'text' => 'Itineraries',
									],
								],
							],
							[
								'quark/secondary-navigation-item',
								[
									'title' => "What's Onboard",
									'url'   => [
										'url'  => 'life-onboard',
										'text' => "What's Onboard",
									],
								],
							],
							[
								'quark/secondary-navigation-item',
								[
									'title' => "What's Included",
									'url'   => [
										'url'  => 'whats-included',
										'text' => "What's Included",
									],
								],
							],
							[
								'quark/secondary-navigation-item',
								[
									'title' => 'Know Before You Go',
									'url'   => [
										'url'  => 'know-before-you-go',
										'text' => 'Know Before You Go',
									],
								],
							],
						],
					],
					[
						'quark/secondary-navigation-cta-buttons',
						[],
						[
							[
								'quark/buttons',
								[],
								[
									[
										'quark/button',
										[
											'btnText'   => 'View Departures',
											'isSizeBig' => true,
										],
									],
									[
										'quark/button',
										[
											'btnText'         => 'Request a Quote',
											'backgroundColor' => 'black',
											'isSizeBig'       => true,
										],
									],
								],
							],
						],
					],
				],
			],
			[
				'quark/two-columns',
				[
					'hasBorder' => false,
					'anchor'    => 'overview',
				],
				[
					[
						'quark/column',
						[],
						[
							[
								'core/heading',
								[
									'level'   => 2,
									'content' => 'Expedition Overview',
								],
							],
							[
								'core/paragraph',
								[],
							],
						],
					],
					[
						'quark/column',
						[],
						[
							[
								'quark/highlights',
								[
									'title' => 'Highlights',
									'info'  => 'Plus, add on adventure options, such as...',
								],
							],
						],
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => 'Find Your Ideal Itinerary',
					'titleAlignment' => 'left',
					'headingLevel'   => '2',
					'hasDescription' => true,
					'anchor'         => 'itineraries',
				],
				[
					[
						'quark/itineraries',
						[],
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => 'Your Life Onboard',
					'titleAlignment' => 'left',
					'headingLevel'   => '2',
					'hasDescription' => true,
					'anchor'         => 'life-onboard',
				],
				[
					[
						'quark/fancy-video',
						[],
					],
				],
			],
			[
				'quark/featured-media-accordions',
				[],
			],
			[
				'quark/ships',
				[],
			],
			[
				'quark/section',
				[
					'title'          => "What's Included",
					'titleAlignment' => 'left',
					'headingLevel'   => '2',
					'hasDescription' => true,
					'anchor'         => 'whats-included',
				],
				[
					[
						'quark/icon-info-grid',
						[],
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => 'Included Activities',
					'titleAlignment' => 'left',
					'headingLevel'   => '2',
				],
				[
					[
						'quark/included-activities',
						[],
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => 'Upgrade Your Expedition',
					'titleAlignment' => 'left',
					'headingLevel'   => '2',
					'hasDescription' => true,
					'description'    => 'Find out what add-on options are available for your expedition.',
				],
				[
					[
						'quark/section',
						[
							'title'          => 'Adventure Options',
							'titleAlignment' => 'left',
							'headingLevel'   => '3',
							'hasDescription' => true,
							'description'    => 'These can be booked in advance at an extra cost. Activities vary by itinerary, destination and are weather permitting.',
						],
						[
							[
								'quark/related-adventure-options',
								[
									'showDescription' => false,
								],
							],
						],
					],
					[
						'quark/section',
						[
							'title'          => 'Trip Extensions',
							'titleAlignment' => 'left',
							'headingLevel'   => '3',
							'hasDescription' => true,
						],
						[
							[
								'quark/trip-extensions',
								[],
							],
						],
					],
					[
						'quark/section',
						[
							'title'          => 'Possible Excursions',
							'titleAlignment' => 'left',
							'headingLevel'   => '3',
							'hasDescription' => true,
						],
						[
							[
								'quark/excursion-accordion',
								[],
							],
						],
					],
				],
			],
			[
				'quark/book-departures-expeditions',
				[],
			],
			[
				'quark/cta-banner',
				[],
			],
			[
				'quark/section',
				[
					'title'          => 'Know Before You Go',
					'titleAlignment' => 'left',
					'headingLevel'   => '2',
					'anchor'         => 'know-before-you-go',
				],
				[
					[
						'quark/accordion',
						[],
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => 'Your Expedition Team',
					'titleAlignment' => 'left',
					'headingLevel'   => '2',
				],
				[
					[
						'quark/staff-members',
						[
							'selection'  => 'manual',
							'isCarousel' => true,
						],
					],
				],
			],
		],
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
}

/**
 * Register Destination taxonomy.
 *
 * @return void
 */
function register_destination_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Destinations',
		'singular_name'              => 'Destination',
		'search_items'               => 'Search Destinations',
		'popular_items'              => 'Popular Destinations',
		'all_items'                  => 'All Destinations',
		'parent_item'                => 'Parent Destination',
		'parent_item_colon'          => 'Parent Destination:',
		'edit_item'                  => 'Edit Destination',
		'update_item'                => 'Update Destination',
		'add_new_item'               => 'Add New Destination',
		'new_item_name'              => 'New Destination',
		'separate_items_with_commas' => 'Separate Destinations with commas',
		'add_or_remove_items'        => 'Add or remove Destinations',
		'choose_from_most_used'      => 'Choose from the most used Destinations',
		'menu_name'                  => 'Destinations',
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
	register_taxonomy( DESTINATION_TAXONOMY, (array) apply_filters( 'qe_destination_taxonomy_post_types', [] ), $args );
}

/**
 * Register Expedition Category taxonomy.
 *
 * @return void
 */
function register_expedition_category_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Expedition Categories',
		'singular_name'              => 'Expedition Category',
		'search_items'               => 'Search Expedition Categories',
		'popular_items'              => 'Popular Expedition Categories',
		'all_items'                  => 'All Expedition Categories',
		'parent_item'                => 'Parent Expedition Category',
		'parent_item_colon'          => 'Parent Expedition Category:',
		'edit_item'                  => 'Edit Expedition Category',
		'update_item'                => 'Update Expedition Category',
		'add_new_item'               => 'Add New Expedition Category',
		'new_item_name'              => 'New Expedition Category',
		'separate_items_with_commas' => 'Separate Expedition Categories with commas',
		'add_or_remove_items'        => 'Add or remove Expedition Categories',
		'choose_from_most_used'      => 'Choose from the most used Expedition Categories',
		'menu_name'                  => 'Expedition Categories',
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
	register_taxonomy( EXPEDITION_CATEGORY_TAXONOMY, (array) apply_filters( 'qe_expedition_category_taxonomy_post_types', [] ), $args );
}

/**
 * Register Expedition tag taxonomy.
 *
 * @return void
 */
function register_expedition_tag_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Expedition Tags',
		'singular_name'              => 'Expedition Tag',
		'search_items'               => 'Search Expedition Tags',
		'popular_items'              => 'Popular Expedition Tags',
		'all_items'                  => 'All Expedition Tags',
		'parent_item'                => 'Parent Expedition Tag',
		'parent_item_colon'          => 'Parent Expedition Tag:',
		'edit_item'                  => 'Edit Expedition Tag',
		'update_item'                => 'Update Expedition Tag',
		'add_new_item'               => 'Add New Expedition Tag',
		'new_item_name'              => 'New Expedition Tag',
		'separate_items_with_commas' => 'Separate Expedition Tags with commas',
		'add_or_remove_items'        => 'Add or remove Expedition Tags',
		'choose_from_most_used'      => 'Choose from the most used Expedition Tags',
		'menu_name'                  => 'Expedition Tags',
	];

	// Prepare args for registering taxonomy.
	$args = [
		'labels'            => $labels,
		'public'            => false,
		'show_in_nav_menus' => true,
		'show_ui'           => true,
		'show_tagcloud'     => false,
		'show_admin_column' => true,
		'hierarchical'      => false,
		'rewrite'           => false,
		'query_var'         => true,
		'capabilities'      => [],
		'show_in_rest'      => true,
	];

	// Register taxonomy.
	register_taxonomy( EXPEDITION_TAG_TAXONOMY, (array) apply_filters( 'qe_expedition_tag_taxonomy_post_types', [] ), $args );
}

/**
 * Register Departure Destinations taxonomy.
 *
 * @return void
 */
function register_excursion_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Excursions',
		'singular_name'              => 'Excursion',
		'search_items'               => 'Search Excursion',
		'popular_items'              => 'Popular Excursions',
		'all_items'                  => 'All Excursions',
		'parent_item'                => 'Parent Excursion',
		'parent_item_colon'          => 'Parent Excursion:',
		'edit_item'                  => 'Edit Excursion',
		'update_item'                => 'Update Excursion',
		'add_new_item'               => 'Add New Excursion',
		'new_item_name'              => 'New Excursion',
		'separate_items_with_commas' => 'Separate Excursions with commas',
		'add_or_remove_items'        => 'Add or remove Excursions',
		'choose_from_most_used'      => 'Choose from the most used Excursions',
		'menu_name'                  => 'Excursions',
	];

	// Prepare args for registering taxonomy.
	$args = [
		'labels'            => $labels,
		'public'            => false,
		'show_in_nav_menus' => false,
		'show_ui'           => true,
		'show_tagcloud'     => false,
		'show_admin_column' => false,
		'hierarchical'      => true,
		'rewrite'           => false,
		'query_var'         => true,
		'capabilities'      => [],
		'show_in_rest'      => true,
		'meta_box_cb'       => false,
	];

	// Register taxonomy.
	register_taxonomy( EXCURSION_TAXONOMY, (array) apply_filters( 'qe_excursion_taxonomy_post_types', [] ), $args );
}

/**
 * Hide Departure Destination taxonomy metabox.
 *
 * @param WP_REST_Response|null $response The response object.
 * @param WP_Taxonomy| null     $taxonomy The original taxonomy object.
 * @param WP_REST_Request|null  $request  Request used to generate the response.
 *
 * @return WP_REST_Response|null
 */
function hide_excursion_metabox( WP_REST_Response $response = null, WP_Taxonomy $taxonomy = null, WP_REST_Request $request = null ): WP_REST_Response|null {
	// Check if taxonomy is Icon.
	if (
		! $taxonomy instanceof WP_Taxonomy
		|| ! $response instanceof WP_REST_Response
		|| ! $request instanceof WP_REST_Request
		|| EXCURSION_TAXONOMY !== $taxonomy->name
	) {
		return $response;
	}

	// Get context.
	$context = ! empty( $request['context'] ) ? $request['context'] : 'view';

	// Context is edit in the editor.
	if ( 'edit' === $context && false === $taxonomy->meta_box_cb ) {
		$data_response = $response->get_data();

		// Check if data response is not an array.
		if ( ! is_array( $data_response ) ) {
			$data_response = [];
		}

		// Hide UI.
		$data_response['visibility']['show_ui'] = false;
		$response->set_data( $data_response );
	}

	// Return response.
	return $response;
}

/**
 * Opt into stuff.
 *
 * @param string[] $post_types Post types.
 *
 * @return string[]
 */
function opt_in( array $post_types = [] ): array {
	// Append Expeditions post type for taxonomy.
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
	do_action( 'qe_expedition_post_cache_busted', $post_id );
}

/**
 * SEO structured data for this post type.
 *
 * @param mixed[] $schema SEO schema.
 *
 * @return mixed[]
 */
function seo_structured_data( array $schema = [] ): array {
	// If current post is not a expedition post, return the schema as is.
	if ( ! is_singular( POST_TYPE ) ) {
		return $schema;
	}

	// Add structured data for this post.
	$schema[] = get_seo_structured_data( absint( get_the_ID() ) );

	// Return updated schema.
	return $schema;
}

/**
 * Get structured data for an expedition.
 *
 * @param int $post_id Post ID.
 *
 * @return array<int, array{
 *     '@context': string,
 *     '@type': string,
 *     name: string,
 *     description: string,
 *     brand?: string,
 *     url?: string,
 *     image?: string|false,
 *     subTrip?: array<int, array{
 *         '@type': string,
 *         name: string,
 *         itinerary: array<int, array{
 *             '@type': string,
 *             name: string,
 *             description: string,
 *         }>,
 *     }>
 * }>
 */
function get_seo_structured_data( int $post_id = 0 ): array {
	// Get expedition.
	$expedition = get( $post_id );

	// Bail if no expedition.
	if ( ! $expedition['post'] instanceof WP_Post ) {
		return [];
	}

	// Initialize data.
	$data                = [];
	$product_schema      = [];
	$tourist_trip_schema = [];

	// Add product schema.
	$product_schema = [
		'@context'    => 'https://schema.org',
		'@type'       => 'Product',
		'name'        => $expedition['post']->post_title,
		'description' => wp_trim_words( get_the_excerpt( $expedition['post'] ), 35, '...' ),
		'brand'       => 'Quark Expeditions',
		'url'         => $expedition['permalink'],
		'image'       => get_the_post_thumbnail_url( $post_id ),
	];

	// Get starting from price.
	$expedition_price = get_starting_from_price( $post_id )['discounted'];

	// Add offers schema.
	if ( ! empty( $expedition_price ) ) {
		$product_schema['offers'] = [
			'@type'         => 'Offer',
			'price'         => $expedition_price,
			'priceCurrency' => 'USD',
			'url'           => $expedition['permalink'],
		];
	}

	// Add tourist trip schema.
	$tourist_trip_schema = [
		'@context'    => 'https://schema.org',
		'@type'       => 'TouristTrip',
		'name'        => $expedition['post']->post_title,
		'description' => wp_trim_words( get_the_excerpt( $expedition['post'] ), 35, '...' ),
	];

	// Get related Itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( ! empty( $itineraries ) ) {
		// Initialize itinerary schema.
		$itinerary_schema = [];

		// Loop through itineraries.
		foreach ( $itineraries as $itinerary ) {
			// Check for itinerary.
			if ( ! $itinerary['post'] instanceof WP_Post || empty( $itinerary['post_meta'] ) || empty( $itinerary['post_meta']['itinerary_days'] ) ) {
				continue;
			}

			// Initialize Itinerary Days schema.
			$itinerary_days_schema = [];

			// Get the Itinerary Days.
			$itinerary_days = $itinerary['post_meta']['itinerary_days'];

			// Check for array.
			if ( ! is_array( $itinerary_days ) ) {
				continue;
			}

			// Loop through Itinerary Days.
			foreach ( $itinerary_days as $itinerary_day ) {
				// Get Itinerary Day post.
				$itinerary_day_post = get_itinerary_day( absint( $itinerary_day ) );

				// Check for Itinerary Day.
				if ( ! $itinerary_day_post['post'] instanceof WP_Post ) {
					continue;
				}

				// Add Itinerary Day schema.
				$itinerary_days_schema[] = [
					'@type'       => 'TouristAttraction',
					'name'        => format_itinerary_day_title( absint( $itinerary_day ) ),
					'description' => wp_strip_all_tags( $itinerary_day_post['post']->post_content ),
				];
			}

			// Get Itinerary name.
			$itinerary_name = $itinerary['post']->post_title;

			// Check for Itinerary name containing a colon.
			$colon_position = strstr( $itinerary_name, ':' );

			// Remove colon and space from Itinerary name.
			if ( false !== $colon_position ) {
				$itinerary_name = ltrim( $colon_position, ': ' );
			}

			// Add itinerary schema.
			$itinerary_schema[] = [
				'@type'     => 'Trip',
				'name'      => $itinerary_name,
				'itinerary' => $itinerary_days_schema,
			];
		}

		// Add itinerary schema to tourist trip schema.
		$tourist_trip_schema['subTrip'] = $itinerary_schema;
	}

	// Add the schemas to the data.
	$data[] = $product_schema;
	$data[] = $tourist_trip_schema;

	// Return built data.
	return $data;
}

/**
 * Get an Expedition.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     post_meta: mixed[],
 *     post_taxonomies: mixed[],
 *     permalink: string,
 *     data: mixed[],
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
			'data'            => $cached_value['data'] ?? [],
			'permalink'       => $cached_value['permalink'],
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
			'data'            => [],
			'permalink'       => '',
		];
	}

	// Get expedition block attrs.
	$data = parse_block_attributes( $post );

	// Build data.
	$data = [
		'post'            => $post,
		'post_meta'       => [],
		'post_taxonomies' => [],
		'data'            => $data,
		'permalink'       => strval( get_permalink( $post ) ? : '' ),
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
 * Parse block attributes.
 *
 * @param WP_Post|null $post Post object.
 *
 * @return array{}|array{
 *    hero_card_slider_image_ids: int[],
 * }
 */
function parse_block_attributes( WP_Post $post = null ): array {
	// Check for post.
	if ( ! $post instanceof WP_Post ) {
		return [];
	}

	// Prase blocks.
	$blocks = parse_blocks( $post->post_content );

	// Skip if no blocks.
	if ( empty( $blocks ) ) {
		return [];
	}

	// Flatten blocks.
	$flattened_blocks = _flatten_blocks( $blocks );

	// Initialize attributes.
	$hero_card_slider_image_ids = [];

	// Loop through blocks.
	foreach ( $flattened_blocks as $block ) {
		// Check for block name.
		if ( 'quark/hero-card-slider' === $block['blockName'] ) {
			if ( ! isset( $block['attrs'] ) || ! is_array( $block['attrs'] ) ) {
				continue;
			}

			// Check if items present.
			if ( ! isset( $block['attrs']['items'] ) || ! is_array( $block['attrs']['items'] ) ) {
				continue;
			}

			// Loop through items.
			foreach ( $block['attrs']['items'] as $item ) {
				// Check for item.
				if ( ! is_array( $item ) ) {
					continue;
				}

				// Check for image id.
				if ( empty( $item['id'] ) ) {
					continue;
				}

				// Add image id to array.
				$hero_card_slider_image_ids[] = $item['id'];
			}
		}
	}

	// Return attributes.
	return [
		'hero_card_slider_image_ids' => $hero_card_slider_image_ids,
	];
}

/**
 * Get Region for the expedition.
 *
 * @param int $post_id Post ID.
 *
 * @return array{}| array{
 *     array{
 *         term_id: int,
 *         name: string,
 *         slug: string,
 *         taxonomy: string,
 *         description: string,
 *         parent: int,
 *         term_group: int,
 *     }
 * }
 */
function get_region_terms( int $post_id = 0 ): array {
	// Get post.
	$post   = get( $post_id );
	$region = [];

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $region;
	}

	// Get parent of qrk_destination taxonomy.
	if (
		array_key_exists( DESTINATION_TAXONOMY, $post['post_taxonomies'] )
		&& is_array( $post['post_taxonomies'][ DESTINATION_TAXONOMY ] )
	) {
		// Loop through taxonomy and get all with no parent term name.
		foreach ( $post['post_taxonomies'][ DESTINATION_TAXONOMY ] as $term ) {
			if ( empty( $term['parent'] ) ) {
				$region[] = $term;
			}
		}
	}

	// Return regions.
	return $region;
}

/**
 * Get Expedition Category for the expedition.
 *
 * @param int $post_id Post ID.
 *
 * @return array{}| array{
 *     array{
 *         term_id: int,
 *         name: string,
 *         slug: string,
 *         taxonomy: string,
 *         description: string,
 *         parent: int,
 *         term_group: int,
 *     }
 * }
 */
function get_expedition_category_terms( int $post_id = 0 ): array {
	// Get post.
	$post       = get( $post_id );
	$categories = [];

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $categories;
	}

	// Get parent of qrk_expedition_category taxonomy.
	if (
		array_key_exists( EXPEDITION_CATEGORY_TAXONOMY, $post['post_taxonomies'] )
		&& is_array( $post['post_taxonomies'][ EXPEDITION_CATEGORY_TAXONOMY ] )
	) {
		// Loop through taxonomy and get all with no parent term name.
		foreach ( $post['post_taxonomies'][ EXPEDITION_CATEGORY_TAXONOMY ] as $term ) {
			if ( empty( $term['parent'] ) ) {
				$categories[] = $term;
			}
		}
	}

	// Return regions.
	return $categories;
}

/**
 * Get Itineraries for the expedition.
 *
 * @param int $post_id Post ID.
 *
 * @return array{}| array{
 *     array{
 *      post: WP_Post|null,
 *      post_meta: mixed[],
 *      post_taxonomies: mixed[]
 *  }
 * }
 */
function get_itineraries( int $post_id = 0 ): array {
	// Get post.
	$post        = get( $post_id );
	$itineraries = [];

	// Check for post_meta.
	if ( empty( $post['post_meta'] ) || empty( $post['post_meta']['related_itineraries'] ) ) {
		return $itineraries;
	}

	// Get Itineraries from related_itineraries post meta.
	$itinerary_ids = $post['post_meta']['related_itineraries'];

	// Check for itinerary_ids array.
	if ( ! is_array( $itinerary_ids ) ) {
		return $itineraries;
	}

	// Check for Itinerary IDs.
	foreach ( $itinerary_ids as $itinerary_id ) {
		// Get Itinerary.
		$itinerary = get_itinerary( absint( $itinerary_id ) );

		// Check for Itinerary.
		if ( empty( $itinerary['post'] ) || ! $itinerary['post'] instanceof WP_Post ) {
			return $itineraries;
		}

		// Check Itinerary is published.
		if ( 'publish' !== $itinerary['post']->post_status ) {
			continue;
		}

		// Add Itinerary to array.
		$itineraries[] = $itinerary;
	}

	// Return Itineraries.
	return $itineraries;
}

/**
 * Get Minimum Duration days for Expedition.
 * From set Itineraries.
 *
 * @param int $post_id Post ID.
 *
 * @return int
 */
function get_minimum_duration( int $post_id = 0 ): int {
	// Get post.
	$post = get( $post_id );

	// Minimum duration.
	$minimum_duration = 0;

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $minimum_duration;
	}

	// Get itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( empty( $itineraries ) ) {
		return $minimum_duration;
	}

	// Loop through itineraries and get minimum duration.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if ( ! is_array( $itinerary ) || empty( $itinerary['post_meta'] ) || empty( $itinerary['post_meta']['duration_in_days'] ) ) {
			continue;
		}

		// Get duration.
		$duration = absint( $itinerary['post_meta']['duration_in_days'] );

		// Check minimum duration.
		if ( empty( $minimum_duration ) || $duration < $minimum_duration ) {
			$minimum_duration = $duration;
		}
	}

	// Return minimum duration.
	return $minimum_duration;
}

/**
 * Get Minimum Duration Itinerary for Expedition.
 * From set Itineraries.
 *
 * @param int $post_id Post ID.
 *
 * @return WP_Post|null
 */
function get_minimum_duration_itinerary( int $post_id = 0 ): WP_Post|null {
	// Initialize minimum duration.
	$minimum_duration           = 0;
	$minimum_duration_itinerary = null;

	// Get itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( empty( $itineraries ) ) {
		return $minimum_duration_itinerary;
	}

	// Loop through itineraries and get minimum duration.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if ( ! is_array( $itinerary ) || empty( $itinerary['post_meta'] ) || empty( $itinerary['post_meta']['duration_in_days'] ) ) {
			continue;
		}

		// Get duration.
		$duration = absint( $itinerary['post_meta']['duration_in_days'] );

		// Check minimum duration.
		if ( empty( $minimum_duration ) || $duration < $minimum_duration ) {
			$minimum_duration           = $duration;
			$minimum_duration_itinerary = $itinerary['post'];
		}
	}

	// Return minimum duration.
	return $minimum_duration_itinerary;
}

/**
 * Get Starting From Price for Expedition.
 * From set Itineraries.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *    original: int,
 *    discounted: int,
 * }
 */
function get_starting_from_price( int $post_id = 0 ): array {
	// Default starting from price.
	$lowest_prices = [
		'original'   => 0,
		'discounted' => 0,
	];

	// Bail if no post ID.
	if ( empty( $post_id ) ) {
		return $lowest_prices;
	}

	// Current currency.
	$currency = get_current_currency();

	// Get post.
	$post = get( $post_id );

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $lowest_prices;
	}

	// Get itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( empty( $itineraries ) ) {
		return $lowest_prices;
	}

	// Loop through itineraries and get minimum price.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if (
			! is_array( $itinerary )
			|| empty( $itinerary['post'] )
			|| ! $itinerary['post'] instanceof WP_Post
		) {
			continue;
		}

		// Get lowest price for Itinerary.
		$price = get_itinerary_lowest_price( $itinerary['post']->ID, $currency );

		// Check minimum price.
		if ( ! empty( $price['discounted'] ) && ( empty( $lowest_price ) || $price['discounted'] < $lowest_price ) ) {
			$lowest_price  = $price['discounted'];
			$lowest_prices = $price;
		}
	}

	// Return starting from price.
	return $lowest_prices;
}

/**
 * Get Starting From Locations for Expedition.
 * From set Itineraries.
 *
 * @param int $post_id Post ID.
 *
 * @return array{} | array{
 *     array{
 *        title: string,
 *     },
 * }
 */
function get_starting_from_locations( int $post_id = 0 ): array {
	// Get post.
	$post = get( $post_id );

	// Starting from locations.
	$starting_from_locations = [];

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $starting_from_locations;
	}

	// Get itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( empty( $itineraries ) ) {
		return $starting_from_locations;
	}

	// Loop through itineraries and get minimum price.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if (
			! is_array( $itinerary )
			|| empty( $itinerary['post'] )
			|| ! $itinerary['post'] instanceof WP_Post
			|| empty( $itinerary['post_meta']['start_location'] )
		) {
			continue;
		}

		// Get location.
		$location      = absint( $itinerary['post_meta']['start_location'] );
		$location_term = get_term_by( 'id', $location, DEPARTURE_LOCATION_TAXONOMY );

		// Check location.
		if ( $location_term instanceof WP_Term ) {
			// Add unique location to array.
			$starting_from_locations[ $location_term->term_id ] = [
				'title' => $location_term->name,
			];
		}
	}

	// Return starting from locations.
	return $starting_from_locations;
}

/**
 * Get Ships for Expedition.
 * From set Itineraries.
 *
 * @param int $post_id Post ID.
 *
 * @return array{} | array{
 *     array{
 *         post: WP_Post,
 *         post_meta: mixed[],
 *         permalink: string,
 *     },
 * }
 */
function get_ships( int $post_id = 0 ): array {
	// Get post.
	$post = get( $post_id );

	// Ships.
	$ships = [];

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $ships;
	}

	// Get itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( empty( $itineraries ) ) {
		return $ships;
	}

	// Loop through itineraries and get minimum price.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if (
			! is_array( $itinerary )
			|| empty( $itinerary['post'] )
			|| ! $itinerary['post'] instanceof WP_Post
		) {
			continue;
		}

		// Get related ships.
		$related_ship_post_ids = get_related_ships( $itinerary['post']->ID );

		// Loop through related ships.
		foreach ( $related_ship_post_ids as $ship_post_id ) {
			// Get Ship.
			$ship = get_ship( $ship_post_id );

			// Check for Ship.
			if ( empty( $ship['post'] ) || ! $ship['post'] instanceof WP_Post ) {
				continue;
			}

			// Add Ship to array.
			$ships[ $ship_post_id ] = $ship;
		}
	}

	// Return ships.
	return $ships;
}

/**
 * Get Total Departures for Expedition.
 * From set Itineraries.
 *
 * @param int $post_id Post ID.
 *
 * @return int
 */
function get_total_departures( int $post_id = 0 ): int {
	// Get post.
	$post = get( $post_id );

	// Total departures.
	$total_departures = 0;

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $total_departures;
	}

	// Get itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( empty( $itineraries ) ) {
		return $total_departures;
	}

	// Loop through itineraries and get minimum price.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if (
			! is_array( $itinerary )
			|| empty( $itinerary['post'] )
			|| ! $itinerary['post'] instanceof WP_Post
		) {
			continue;
		}

		// Get total departures for Itinerary.
		$total_departures = $total_departures + count( get_departures_by_itinerary( $itinerary['post']->ID ) );
	}

	// Return total departures.
	return $total_departures;
}

/**
 * Get Starting From Date for Expedition.
 * From set Itineraries.
 *
 * @param int $post_id Post ID.
 *
 * @return string
 */
function get_starting_from_date( int $post_id = 0 ): string {
	// Get post.
	$post = get( $post_id );

	// Starting from date.
	$starting_from_date = '';

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $starting_from_date;
	}

	// Get itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( empty( $itineraries ) ) {
		return $starting_from_date;
	}

	// Loop through itineraries and get minimum price.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if (
			! is_array( $itinerary )
			|| empty( $itinerary['post'] )
			|| ! $itinerary['post'] instanceof WP_Post
		) {
			continue;
		}

		// Get starting date.
		$test_date = get_start_date( $itinerary['post']->ID );

		// Check for date.
		if ( empty( $test_date ) ) {
			continue;
		}

		// Check if starting from date is empty or test date is earlier.
		if (
			empty( $starting_from_date )
			|| strtotime( $test_date ) < strtotime( $starting_from_date )
		) {
			$starting_from_date = $test_date;
		}
	}

	// Return starting from date.
	return $starting_from_date;
}

/**
 * Get Ending To Date for Expedition.
 * From set Itineraries.
 *
 * @param int $post_id Post ID.
 *
 * @return string
 */
function get_ending_to_date( int $post_id = 0 ): string {
	// Get post.
	$post = get( $post_id );

	// ending to date.
	$ending_to_date = '';

	// Check for post.
	if ( empty( $post['post'] ) || ! $post['post'] instanceof WP_Post ) {
		return $ending_to_date;
	}

	// Get itineraries.
	$itineraries = get_itineraries( $post_id );

	// Check for itineraries.
	if ( empty( $itineraries ) ) {
		return $ending_to_date;
	}

	// Loop through itineraries and get minimum price.
	foreach ( $itineraries as $itinerary ) {
		// Check for Itinerary.
		if (
			! is_array( $itinerary )
			|| empty( $itinerary['post'] )
			|| ! $itinerary['post'] instanceof WP_Post
		) {
			continue;
		}

		// Get ending date.
		$test_date = get_end_date( $itinerary['post']->ID );

		// Check for date.
		if ( empty( $test_date ) ) {
			continue;
		}

		// Check if ending to date is empty or test date is later.
		if (
			empty( $ending_to_date )
			|| strtotime( $test_date ) > strtotime( $ending_to_date )
		) {
			$ending_to_date = $test_date;
		}
	}

	// Return ending to date.
	return $ending_to_date;
}

/**
 * Format Departure date range for the expedition.
 *
 * @param int $post_id Post ID.
 *
 * @return string
 */
function get_formatted_date_range( int $post_id = 0 ): string {
	// Get starting from and ending to dates.
	$starting_from_date = get_starting_from_date( $post_id );
	$ending_to_date     = get_ending_to_date( $post_id );

	// Check if both dates are empty and return an empty string.
	if ( empty( $starting_from_date ) && empty( $ending_to_date ) ) {
		return '';
	}

	// Check if only the ending to date is empty.
	if ( empty( $ending_to_date ) ) {
		$ending_to_date = $starting_from_date; // Starting From will not be empty here.
	}

	// Get the month and year for both start and end dates.
	$start_month = gmdate( 'F', absint( strtotime( $starting_from_date ) ) );
	$start_year  = gmdate( 'Y', absint( strtotime( $starting_from_date ) ) );
	$end_month   = gmdate( 'F', absint( strtotime( $ending_to_date ) ) );
	$end_year    = gmdate( 'Y', absint( strtotime( $ending_to_date ) ) );

	// Same month and year.
	if ( $start_month === $end_month && $start_year === $end_year ) {
		/* translators: %1$s: Expedition start Month, %2$s: Expedition start Year */
		return sprintf( __( 'in %1$s %2$s', 'qrk' ), $start_month, $start_year );
	}

	// Same year, different months OR Different year.
	/* translators: %1$s: Expedition start Month, %2$s: Expedition start Year, %3$s: Expedition end Month, %4$s: Expedition end Year */
	return sprintf( __( 'between %1$s %2$s to %3$s %4$s', 'qrk' ), $start_month, $start_year, $end_month, $end_year );
}

/**
 * Get Expedition details card data.
 *
 * @param int $post_id Expedition Post ID.
 *
 * @return array{}|array{
 *     title: string,
 *     duration: int,
 *     region: string,
 *     from_price: array{
 *         original: string,
 *         discounted: string,
 *     },
 *     starting_from ?: array{}|array{
 *         array{ title : string },
 *     },
 *     total_departures: int,
 *     ships ?: array{}|array{
 *         array{ title : string },
 *     },
 *     tags ?: array{}|array{
 *         array{ title : string },
 *     },
 *     date_range: string,
 * }
 */
function get_details_data( int $post_id = 0 ): array {
	// Currency.
	$currency = get_current_currency();

	// Check for cached version.
	$cache_key    = CACHE_KEY . "_details_$post_id" . '_' . $currency;
	$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

	// Check for cached value.
	if ( is_array( $cached_value ) && ! empty( $cached_value ) ) {
		return $cached_value;
	}

	// Get post.
	$post = get( $post_id );
	$data = [];

	// Check for post.
	if (
		empty( $post['post'] )
		|| ! $post['post'] instanceof WP_Post
		|| POST_TYPE !== $post['post']->post_type
	) {
		return $data;
	}

	// Break title with colon.
	$title_parts = explode( ':', $post['post']->post_title );

	// Check if title parts are available.
	if ( ! empty( $title_parts[0] ) ) {
		$title     = trim( $title_parts[0] );
		$sub_title = trim( $title_parts[1] ?? '' );
	} else {
		$title     = $post['post']->post_title;
		$sub_title = '';
	}

	// Set title.
	$data['title']     = $title;
	$data['sub_title'] = $sub_title;

	// Init $tags.
	$tags = [];

	// Get Expeditions tags.
	if ( array_key_exists( EXPEDITION_TAG_TAXONOMY, $post['post_taxonomies'] ) ) {
		$tags = $post['post_taxonomies'][ EXPEDITION_TAG_TAXONOMY ];
	}

	// Check for tags.
	if ( ! empty( $tags ) ) {
		// Loop through $tags and fetch tag name to title key.
		foreach ( $tags as $tag ) {
			if ( ! is_array( $tag ) || empty( $tag['name'] ) ) {
				continue;
			}

			// Add tag name to array.
			$data['tags'][] = [
				'title' => $tag['name'],
			];
		}
	}

	// Get Regions.
	$regions     = get_region_terms( $post_id );
	$data_region = [];

	// Check for regions.
	if ( ! empty( $regions ) ) {
		// Get region name comma seperated.
		foreach ( $regions as $region ) {
			if ( ! is_array( $region ) || empty( $region['name'] ) ) {
				continue;
			}

			// Add region name to array.
			$data_region[] = $region['name'];
		}
	}

	// Set region name.
	$data['region'] = implode( ', ', $data_region );

	// Set minimum duration.
	$data['duration'] = get_minimum_duration( $post_id );

	// Set starting from price.
	$prices             = get_starting_from_price( $post_id );
	$data['from_price'] = [
		'original'   => format_price( $prices['original'], $currency ),
		'discounted' => format_price( $prices['discounted'], $currency ),
	];

	// Set starting from locations list.
	$data['starting_from'] = get_starting_from_locations( $post_id );

	// Set ships.
	$ships_data = get_ships( $post_id );

	// Check for ships.
	if ( ! empty( $ships_data ) ) {
		foreach ( $ships_data as $ship ) {
			if ( ! $ship['post'] instanceof WP_Post ) {
				continue;
			}

			// Add ship name/title to array.
			$data['ships'][] = [
				'title' => $ship['post']->post_title,
			];
		}
	}

	// Get total number of Departures.
	$data['total_departures'] = get_total_departures( $post_id );

	// get date range.
	$data['date_range'] = get_formatted_date_range( $post_id );

	// Set cache and return data.
	wp_cache_set( $cache_key, $data, CACHE_GROUP );

	// Return data.
	return $data;
}

/**
 * Bust cache for Details Card.
 *
 * @param int $post_id Post ID.
 *
 * @return void
 */
function bust_details_cache( int $post_id = 0 ): void {
	// Currencies.
	$currencies = get_currencies();

	// Loop through currencies and bust cache.
	foreach ( $currencies as $currency ) {
		// Clear cache for this post.
		wp_cache_delete( CACHE_KEY . "_details_$post_id" . '_' . $currency, CACHE_GROUP );
	}
}

/**
 * Bust cache for Details Card from Itinerary.
 *
 * @param int $itinerary_id Itinerary Post ID.
 *
 * @return void
 */
function bust_details_cache_on_itinerary_update( int $itinerary_id = 0 ): void {
	// Get post ID.
	if ( 0 === $itinerary_id ) {
		$itinerary_id = absint( get_the_ID() );
	}

	// Get related Expedition ID.
	$expedition_id = absint( get_post_meta( $itinerary_id, 'related_expedition', true ) );

	// Validate for Expedition ID.
	if ( empty( $expedition_id ) ) {
		return;
	}

	// Bust cache for Expedition.
	bust_details_cache( $expedition_id );
}

/**
 * Bust cache for Details Card from Departure.
 *
 * @param int $departure_id Departure Post ID.
 *
 * @return void
 */
function bust_details_cache_on_departure_update( int $departure_id = 0 ): void {
	// Get post ID.
	if ( 0 === $departure_id ) {
		$departure_id = absint( get_the_ID() );
	}

	// Get post.
	$departure = get_departure( $departure_id );

	// Check for Departure.
	if ( empty( $departure['post'] ) || ! $departure['post'] instanceof WP_Post ) {
		return;
	}

	// Get related Expedition ID.
	$expedition_id = $departure['post_meta']['related_expedition'] ?? 0;

	// Validate for Expedition ID.
	if ( empty( $expedition_id ) || ! absint( $expedition_id ) ) {
		return;
	}

	// Bust cache for Expedition.
	bust_details_cache( absint( $expedition_id ) );
}

/**
 * Get Expedition Details Card.
 *
 * @param int $expedition_id Expedition Post ID.
 *
 * @return array<int, int>
 */
function get_expedition_ship_ids( int $expedition_id = 0 ): array {
	// Get the expedition.
	$expedition      = get( $expedition_id );
	$expedition_post = $expedition['post'];
	$ships_ids       = [];

	// Check for post.
	if ( ! $expedition_post instanceof WP_Post ) {
		return $ships_ids;
	}

	// Get the ships IDs.
	$ships = get_ships( $expedition_post->ID );

	// Check for ships.
	if ( empty( $ships ) ) {
		return $ships_ids;
	}

	// Get the ships IDs.
	foreach ( $ships as $ship ) {
		$ships_ids[] = absint( $ship['post']->ID );
	}

	// Return the ships IDs.
	return array_unique( $ships_ids );
}

/**
 * Get Destination term by Softrip code.
 *
 * @param string $code Softrip code.
 *
 * @return null|WP_Term
 */
function get_destination_term_by_code( string $code = '' ): null|WP_Term {
	// Check if code is empty.
	if ( empty( $code ) ) {
		return null;
	}

	// Prepare arguments for the query.
	$args = [
		'taxonomy'   => DESTINATION_TAXONOMY,
		'hide_empty' => false,
		'number'     => 1,
		'meta_query' => [
			[
				'key'     => 'softrip_id',
				'value'   => $code,
				'compare' => '=',
			],
		],
	];

	// Perform the query.
	$terms = get_terms( $args );

	// Check if any terms are found.
	if ( ! empty( $terms ) && ! $terms instanceof WP_Error && $terms[0] instanceof WP_Term ) {
		return $terms[0];
	}

	// Return null if no term is found.
	return null;
}

/**
 * Add related Itineraries meta box.
 *
 * @return void
 */
function add_related_itineraries_meta_box(): void {
	// Get current screen detail.
	$screen = get_current_screen();

	// If it's not post type screen then bail out.
	if ( ! $screen instanceof WP_Screen || empty( $screen->post_type ) || POST_TYPE !== $screen->post_type ) {
		return;
	}

	// Add meta box.
	add_meta_box( 'related-itineraries', 'Related Itineraries', __NAMESPACE__ . '\\add_related_itineraries_meta_box_content', [ POST_TYPE ], 'side', 'low' );
}

/**
 * Add related Itineraries meta box content.
 *
 * @param WP_Post|null $post Post object.
 *
 * @return void
 */
function add_related_itineraries_meta_box_content( WP_Post $post = null ): void {
	// Validate is post is not empty and type of current post type.
	if ( ! $post instanceof WP_Post || POST_TYPE !== $post->post_type ) {
		return;
	}

	// Get Related Itineraries meta.
	$related_itineraries = get_post_meta( $post->ID, 'related_itineraries', true );

	// If Related Itineraries is not exists then bail out.
	if ( ! is_array( $related_itineraries ) || empty( $related_itineraries ) ) {
		echo '<p>The itineraries have not been mapped to the expedition.</p>';

		// Return.
		return;
	}

	// List Itineraries.
	foreach ( $related_itineraries as $itinerary_id ) {
		printf(
			'<p>%1$s -- <em>%2$s</em> <a href="%3$s" target="_blank">(Edit)</a></p>',
			esc_html( get_the_title( $itinerary_id ) ),
			esc_html( strval( get_post_status( $itinerary_id ) ) ),
			esc_url( strval( get_edit_post_link( $itinerary_id ) ) )
		);
	}
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
	if ( ! is_singular( POST_TYPE ) ) {
		return $breadcrumbs;
	}

	// Return breadcrumbs.
	return array_merge(
		$breadcrumbs,
		get_breadcrumbs_ancestors( absint( get_the_ID() ) )
	);
}

/**
 * Get breadcrumbs ancestor.
 *
 * @param int $post_id Post ID.
 *
 * @return array{}|array{
 *     array{
 *         title: string,
 *         url: string,
 *     }
 * }
 */
function get_breadcrumbs_ancestors( int $post_id = 0 ): array {
	// Initialize breadcrumbs.
	$breadcrumbs = [];

	// Bail if post ID is not set.
	if ( empty( $post_id ) ) {
		return $breadcrumbs;
	}

	// Get archive page.
	$expeditions_landing_page = absint( get_option( 'options_expeditions_page', 0 ) );

	// Get it's title and URL for breadcrumbs if it's set.
	if ( ! empty( $expeditions_landing_page ) ) {
		$breadcrumbs[] = [
			'title' => get_the_title( $expeditions_landing_page ),
			'url'   => strval( get_permalink( $expeditions_landing_page ) ),
		];
	}

	// Return updated breadcrumbs.
	return $breadcrumbs;
}
