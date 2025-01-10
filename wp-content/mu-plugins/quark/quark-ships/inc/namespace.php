<?php
/**
 * Namespace functions.
 *
 * @package quark-ships
 */

namespace Quark\Ships;

use WP_Post;

use function Quark\ShipDecks\get as get_deck_data;
use function Quark\CabinCategories\get as get_cabin_category_data;

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
	add_filter( 'qe_adventure_options_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );

	// Other hooks. Assigning non-standard priority to avoid race conditions with ACF.
	add_action( 'save_post', __NAMESPACE__ . '\\bust_post_cache', 11 );

	// Add meta keys to be translated while content sync.
	add_filter( 'qrk_translation_meta_keys', __NAMESPACE__ . '\\translate_meta_keys' );

	// Admin stuff.
	if ( is_admin() ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/ships.php';
		require_once __DIR__ . '/../custom-fields/api-data.php';
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
			'thumbnail',
			'revisions',
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
			'slug'       => 'expedition-ships',
			'with_front' => false,
		],
		'capability_type'     => 'post',
		'template'            => [
			[
				'quark/hero',
				[
					'immersive'      => 'bottom',
					'contentOverlap' => false,
				],
				[
					[
						'quark/breadcrumbs',
					],
					[
						'quark/hero-content',
						[],
						[
							[
								'quark/hero-content-left',
								[],
								[
									[
										'quark/hero-title',
										[
											'title' => 'Expedition Ships',
										],
									],
								],
							],
						],
					],
				],
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
										'url'       => 'overview',
										'text'      => '',
										'newWindow' => false,
									],
								],
							],
							[
								'quark/secondary-navigation-item',
								[
									'title' => 'Features & Amenities',
									'url'   => [
										'url'       => 'features-and-amenities',
										'text'      => '',
										'newWindow' => false,
									],
								],
							],
							[
								'quark/secondary-navigation-item',
								[
									'title' => 'Deck Plans & Cabins',
									'url'   => [
										'url'       => 'deck-plans-and-cabins',
										'text'      => '',
										'newWindow' => false,
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
								'quark/button',
								[
									'backgroundColor' => 'black',
									'btnText'         => 'Download Brochure',
									'isSizeBig'       => true,
								],
							],
							[
								'quark/button',
								[
									'btnText'   => 'Upcoming Departures',
									'isSizeBig' => true,
									'url'       => [
										'url'       => '#upcoming-departures',
										'text'      => '',
										'newWindow' => false,
									],
								],
							],
						],
					],
				],
			],
			[
				'quark/section',
				[
					'anchor'         => 'overview',
					'title'          => 'Overview',
					'titleAlignment' => 'left',
					'headingLevel'   => 2,
				],
				[
					[
						'core/paragraph',
					],
					[
						'quark/ship-specifications',
					],
					[
						'quark/collage',
						[],
						[
							[
								'quark/collage-media-item',
							],
							[
								'quark/collage-media-item',
							],
							[
								'quark/collage-media-item',
							],
							[
								'quark/collage-media-item',
							],
						],
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => 'Features of the Vessel',
					'titleAlignment' => 'left',
				],
				[
					[
						'quark/ship-vessel-features',
						[],
						[
							[
								'quark/ship-vessel-features-card',
							],
							[
								'quark/ship-vessel-features-card',
							],
							[
								'quark/ship-vessel-features-card',
							],
						],
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => 'Features & Amenities',
					'titleAlignment' => 'left',
					'anchor'         => 'features-and-amenities',
					'headingLevel'   => 2,
				],
				[
					[
						'quark/ship-features-amenities',
						[],
						[
							[
								'quark/ship-features-amenities-card',
							],
							[
								'quark/ship-features-amenities-card',
							],
							[
								'quark/ship-features-amenities-card',
							],
						],
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => '',
					'titleAlignment' => 'left',
					'hasDescription' => true,
				],
				[
					[
						'quark/media-carousel',
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => '',
					'titleAlignment' => 'left',
				],
				[
					[
						'quark/media-description-cards',
						[],
						[
							[
								'quark/media-description-card',
							],
							[
								'quark/media-description-card',
							],
							[
								'quark/media-description-card',
							],
						],
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => '',
					'titleAlignment' => 'left',
				],
				[
					[
						'quark/ship-related-adventure-options',
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => 'Deck Plans & Cabins',
					'titleAlignment' => 'left',
					'anchor'         => 'deck-plans-and-cabins',
					'headingLevel'   => 2,
				],
				[
					[
						'quark/ship-decks',
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => 'Comparison of All Cabins',
					'titleAlignment' => 'left',
				],
				[
					[
						'quark/ship-cabin-categories',
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => 'Upcoming Departures',
					'anchor'         => 'upcoming-departures',
					'titleAlignment' => 'left',
					'headingLevel'   => 2,
				],
				[
					[
						'quark/book-departures-ships',
					],
				],
			],
		],
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
	// Get post type.
	$post_type = get_post_type( $post_id );

	// Bail out if post type does not match.
	if ( POST_TYPE !== $post_type ) {
		return;
	}

	// Clear cache for this post.
	wp_cache_delete( CACHE_KEY . "_$post_id", CACHE_GROUP );

	// Delete ship lookup cache.
	wp_cache_delete( CACHE_KEY . '_all_ships', CACHE_GROUP );

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
 *     data: mixed[],
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
			'data'            => $cached_value['data'] ?? [],
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
			'data'            => [],
			'post_taxonomies' => [],
		];
	}

	// Get Ship block attrs.
	$data = parse_block_attributes( $post );

	// Build data.
	$data = [
		'post'            => $post,
		'permalink'       => strval( get_permalink( $post ) ? : '' ),
		'post_meta'       => [],
		'data'            => $data,
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
	// Bail out if empty ship code.
	if ( empty( $ship_code ) ) {
		return 0;
	}

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
				m.meta_key = 'ship_code'
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
 *    specifications: array{
 *        cruising_speed?: string,
 *        guests?: string,
 *        ice_class?: string,
 *        length?: string,
 *        life_boats?: string,
 *        registration?: string,
 *        staff_and_crew?: string,
 *        draft?: string,
 *        guest_ratio?: string,
 *        stabilizers?: string,
 *        propulsion?: string,
 *        zodiacs?: string,
 *        voltage?: string,
 *        breadth?: string,
 *        gross_tonnage?: string,
 *        year_built?: string,
 *        year_refurbished?: string,
 *    },
 *    collage_images: array{}|array{
 *        int: array{
 *            id: int,
 *            size: string,
 *            src: string,
 *            width: int,
 *            height: int,
 *            alt: string,
 *            title: string,
 *            caption: string,
 *        },
 *    },
 *    vessel_features: string[],
 *    ship_amenities: string[],
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

	// Prepare ship meta fields.
	$decks_ids            = [];
	$ship_specifications  = [];
	$ship_collage_images  = [];
	$ship_vessel_features = [];
	$ship_amenities       = [];

	// Get Ship collage images.
	if ( ! empty( $ship['data']['collage'] ) && is_array( $ship['data']['collage'] ) ) {
		$ship_collage_images = $ship['data']['collage'];
	}

	// Get Ship vessel features.
	if ( ! empty( $ship['data']['vessel_features'] ) && is_array( $ship['data']['vessel_features'] ) ) {
		$ship_vessel_features = $ship['data']['vessel_features'];
	}

	// Get Ship amenities.
	if ( ! empty( $ship['data']['ship_amenities'] ) && is_array( $ship['data']['ship_amenities'] ) ) {
		$ship_amenities = $ship['data']['ship_amenities'];
	}

	// Get Decks associated with the ship.
	if ( ! empty( $ship_meta['related_decks'] ) && is_array( $ship_meta['related_decks'] ) ) {
		$decks_ids = array_map( 'absint', $ship_meta['related_decks'] );
	}

	// Set ship specifications.
	if ( ! empty( $ship_meta['cruising_speed'] ) ) {
		$ship_specifications['cruising_speed'] = strval( $ship_meta['cruising_speed'] );
	}

	// Check for guest count.
	if ( ! empty( $ship_meta['guests'] ) ) {
		$ship_specifications['guests'] = strval( $ship_meta['guests'] );
	}

	// Check for ice class.
	if ( ! empty( $ship_meta['ice_class'] ) ) {
		$ship_specifications['ice_class'] = strval( $ship_meta['ice_class'] );
	}

	// Check for length.
	if ( ! empty( $ship_meta['length'] ) ) {
		$ship_specifications['length'] = strval( $ship_meta['length'] );
	}

	// Check for lifeboats.
	if ( ! empty( $ship_meta['lifeboats'] ) ) {
		$ship_specifications['life_boats'] = strval( $ship_meta['lifeboats'] );
	}

	// Check for other specifications.
	if ( ! empty( $ship_meta['registration'] ) ) {
		$ship_specifications['registration'] = strval( $ship_meta['registration'] );
	}

	// Check for Staff and Crew.
	if ( ! empty( $ship_meta['staff_and_crew'] ) ) {
		$ship_specifications['staff_and_crew'] = strval( $ship_meta['staff_and_crew'] );
	}

	// Check for draft.
	if ( ! empty( $ship_meta['draft'] ) ) {
		$ship_specifications['draft'] = strval( $ship_meta['draft'] );
	}

	// Check for guest ratio.
	if ( ! empty( $ship_meta['guest_ratio'] ) ) {
		$ship_specifications['guest_ratio'] = strval( $ship_meta['guest_ratio'] );
	}

	// Check for stabilizers.
	if ( ! empty( $ship_meta['stabilizers'] ) ) {
		$ship_specifications['stabilizers'] = strval( $ship_meta['stabilizers'] );
	}

	// Check for propulsion.
	if ( ! empty( $ship_meta['propulsion'] ) ) {
		$ship_specifications['propulsion'] = strval( $ship_meta['propulsion'] );
	}

	// Check for zodiacs.
	if ( ! empty( $ship_meta['zodiacs'] ) ) {
		$ship_specifications['zodiacs'] = strval( $ship_meta['zodiacs'] );
	}

	// Check for voltage.
	if ( ! empty( $ship_meta['voltage'] ) ) {
		$ship_specifications['voltage'] = strval( $ship_meta['voltage'] );
	}

	// Check for breadth.
	if ( ! empty( $ship_meta['breadth'] ) ) {
		$ship_specifications['breadth'] = strval( $ship_meta['breadth'] );
	}

	// Check for gross tonnage.
	if ( ! empty( $ship_meta['gross_tonnage'] ) ) {
		$ship_specifications['gross_tonnage'] = strval( $ship_meta['gross_tonnage'] );
	}

	// Check for year built.
	if ( ! empty( $ship_meta['year_built'] ) ) {
		$ship_specifications['year_built'] = strval( $ship_meta['year_built'] );
	}

	// Check for year refurbished.
	if ( ! empty( $ship_meta['year_refurbished'] ) ) {
		$ship_specifications['year_refurbished'] = strval( $ship_meta['year_refurbished'] );
	}

	// Return ship data.
	return [
		'name'            => $ship_post->post_name,
		'title'           => $ship_post->post_title,
		'permalink'       => $ship['permalink'],
		'description'     => $ship_post->post_excerpt,
		'related_decks'   => $decks_ids,
		'specifications'  => $ship_specifications,
		'collage_images'  => $ship_collage_images,
		'vessel_features' => $ship_vessel_features,
		'ship_amenities'  => $ship_amenities,
	];
}

/**
 * Get cabins and decks for a ship.
 *
 * @param int $ship_id Ship ID.
 *
 * @return array{}|array{
 *     array{
 *         cabin_name: string,
 *         ship_deck: string[],
 *     }
 * }
 */
function get_cabins_and_decks( int $ship_id = 0 ): array {
	// Init results.
	$results = [];

	// Get the ship data.
	$ship = get( $ship_id );

	// Validate ship post.
	if ( empty( $ship['post'] ) || ! $ship['post'] instanceof WP_Post ) {
		return $results;
	}

	// Prepare deck data.
	$decks_ids = [];

	// Get Decks associated with the ship.
	if ( ! empty( $ship['post_meta']['related_decks'] ) && is_array( $ship['post_meta']['related_decks'] ) ) {
		$decks_ids = array_map( 'absint', $ship['post_meta']['related_decks'] );
	}

	// Prepare data.
	$cabin_category_decks = [];

	// Loop through decks.
	foreach ( $decks_ids as $deck_id ) {
		$deck_data = get_deck_data( $deck_id );

		// Validate ship deck post.
		if ( empty( $deck_data['post'] ) || ! $deck_data['post'] instanceof WP_Post ) {
			continue;
		}

		// Get Deck post meta.
		$deck_meta = $deck_data['post_meta'];
		$deck_name = $deck_meta['deck_name'] ?? '';

		// Prepare Cabin Options data.
		$cabin_options_ids = [];

		// Check if we have cabin categories.
		if ( ! empty( $deck_meta['cabin_categories'] ) && is_array( $deck_meta['cabin_categories'] ) ) {
			$cabin_options_ids = array_map( 'absint', $deck_meta['cabin_categories'] );
		}

		// Loop through cabin options.
		foreach ( $cabin_options_ids as $cabin_option_id ) {
			// Get the cabin category.
			$cabin_category = get_cabin_category_data( $cabin_option_id );

			// Validate Cabin Category post.
			if ( empty( $cabin_category['post'] ) || ! $cabin_category['post'] instanceof WP_Post ) {
				continue;
			}

			// Get the post meta.
			$cabin_meta = $cabin_category['post_meta'];

			// Check for Cabin Name meta.
			if ( empty( $cabin_meta['cabin_name'] ) ) {
				continue;
			}

			// Get the cabin name.
			$cabin_name = $cabin_meta['cabin_name'];

			// Initialize array for the cabin category if not set.
			if ( ! isset( $cabin_category_decks[ $cabin_name ] ) ) {
				$cabin_category_decks[ $cabin_name ] = [];
			}

			// Add deck name to the cabin category.
			if ( ! in_array( $deck_name, $cabin_category_decks[ $cabin_name ], true ) ) {
				$cabin_category_decks[ $cabin_name ][] = $deck_name;
			}
		}
	}

	// Prepare the results.
	foreach ( $cabin_category_decks as $cabin_name => $decks ) {
		$results[] = [
			'cabin_name' => $cabin_name,
			'ship_deck'  => $decks,
		];
	}

	// Return data.
	return $results;
}

/**
 * Parse the collage block attributes.
 *
 * @param WP_Post|null $post The post object.
 *
 * @return array{}|array{
 *    collage: array{} | array{
 *       media_type: string,
 *       size: string,
 *       caption: string,
 *       title: string,
 *       video_url: string,
 *       image_id: int,
 *       image ?: array{
 *         int: array{
 *           id: int,
 *           size: string,
 *           src: string,
 *           width: int,
 *           height: int,
 *           alt: string,
 *           title: string,
 *           caption: string,
 *        },
 *      },
 *    },
 *    vessel_features: string[],
 *    ship_amenities: string[],
 * }
 */
function parse_block_attributes( WP_Post $post = null ): array {
	// Check if the post valid WP_Post.
	if ( empty( $post ) || ! $post instanceof WP_Post ) {
		return [];
	}

	// Parse blocks.
	$blocks = parse_blocks( $post->post_content );

	// Skip if we don't have any blocks.
	if ( empty( $blocks ) ) {
		return [];
	}

	// Flatten blocks.
	$blocks = _flatten_blocks( $blocks );

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
					// Initialize media item attributes.
					$media_item_attrs = [];

					// Check if the block is quark/collage-media-item.
					if ( 'quark/collage-media-item' === $inner_block['blockName'] ) {
						// Check attributes are available.
						if ( empty( $inner_block['attrs'] ) && ! is_array( $inner_block['attrs'] ) ) {
							continue;
						}

						// Retrieve attributes.
						$media_item_attrs['media_type'] = ! empty( $inner_block['attrs']['mediaType'] ) ? strval( $inner_block['attrs']['mediaType'] ) : 'image';
						$media_item_attrs['size']       = ! empty( $inner_block['attrs']['size'] ) ? strval( $inner_block['attrs']['size'] ) : 'small';
						$media_item_attrs['caption']    = ! empty( $inner_block['attrs']['caption'] ) ? strval( $inner_block['attrs']['caption'] ) : '';
						$media_item_attrs['video_url']  = ! empty( $inner_block['attrs']['videoUrl'] ) ? strval( $inner_block['attrs']['videoUrl'] ) : '';
						$image                          = $inner_block['attrs']['image'] ?? [];

						// Check if image is available.
						if ( ! empty( $image ) && is_array( $image ) ) {
							$media_item_attrs['image_id'] = ! empty( $image['id'] ) ? absint( $image['id'] ) : 0;
							$media_item_attrs['title']    = ! empty( $image['title'] ) ? strval( $image['title'] ) : '';

							// Add image attributes.
							$media_item_attrs['image'] = [
								'id'      => ! empty( $image['id'] ) ? absint( $image['id'] ) : 0,
								'size'    => ! empty( $image['size'] ) ? strval( $image['size'] ) : '',
								'src'     => ! empty( $image['src'] ) ? strval( $image['src'] ) : '',
								'width'   => ! empty( $image['width'] ) ? strval( $image['width'] ) : '',
								'height'  => ! empty( $image['height'] ) ? strval( $image['height'] ) : '',
								'alt'     => ! empty( $image['alt'] ) ? strval( $image['alt'] ) : '',
								'title'   => ! empty( $image['title'] ) ? strval( $image['title'] ) : '',
								'caption' => ! empty( $image['caption'] ) ? strval( $image['caption'] ) : '',
							];
						}
					}

					// Add media item attributes to collage attributes.
					$collage_attrs[] = $media_item_attrs;
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
		'collage'         => $collage_attrs,
		'vessel_features' => $ship_vessel_features,
		'ship_amenities'  => $ship_amenities,
	];
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
		'deck_plan_image'  => 'attachment',
		'staff_and_crew'   => 'string',
		'draft'            => 'string',
		'registration'     => 'string',
		'guests'           => 'string',
		'guest_ratio'      => 'string',
		'stabilizers'      => 'string',
		'propulsion'       => 'string',
		'lifeboats'        => 'string',
		'zodiacs'          => 'string',
		'voltage'          => 'string',
		'length'           => 'string',
		'ice_class'        => 'string',
		'breadth'          => 'string',
		'gross_tonnage'    => 'string',
		'cruising_speed'   => 'string',
		'year_built'       => 'string',
		'year_refurbished' => 'string',
		'related_decks'    => __NAMESPACE__ . '\\translate_meta_key',
	];

	// Return meta keys to be translated.
	return array_merge( $meta_keys, $extra_keys );
}
