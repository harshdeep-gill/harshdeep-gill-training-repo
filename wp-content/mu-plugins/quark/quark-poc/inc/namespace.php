<?php
/**
 * Namespace functions.
 *
 * @package quark-poc
 */

namespace Quark\POC;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap() : void {
	// Register Hooks.
	add_action( 'init', __NAMESPACE__ . '\\register_post_types' );
	add_action( 'init', __NAMESPACE__ . '\\register_taxonomies' );
}

/**
 * Register taxonomies.
 *
 * @return void
 */
function register_taxonomies() : void {
	/**
	 * Taxonomies configuration list.
	 *
	 * @var array<string, string[]> $taxonomies_list Taxonomies list.
	 */
	$taxonomies_list = [
		'qrk_accommodation_types'          => [
			'label'        => 'Accommodation Types',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_ship',
			],
		],
		'qrk_adventure_option_category'    => [
			'label'        => 'Adventure Options',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_adventure_option',
				'qrk_departure',
			],
		],
		'qrk_audiences'                    => [
			'label'        => 'Audiences',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_marketing_promotion',
			],
		],
		'qrk_cabin_classes'                => [
			'label'        => 'Cabin Classes',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_cabin_category',
			],
		],
		'qrk_charter_companies'            => [
			'label'        => 'Charter Companies',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_departure',
			],
		],
		'qrk_departments'                  => [
			'label'        => 'Departments',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_staff_member',
			],
		],
		'qrk_departure_destinations'       => [
			'label'        => 'Departure Destinations',
			'hierarchical' => true,
			'post_types'   => [],
		],
		'qrk_departure_locations'          => [
			'label'        => 'Departure Locations',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_itinerary',
			],
		],
		'qrk_departure_staff_roles'        => [
			'label'        => 'Departure Staff Roles',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_staff_member',
			],
		],
		'qrk_destinations'                 => [
			'label'        => 'Destinations',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_adventure_option',
				'qrk_expedition',
				'qrk_marketing_promotion',
				'qrk_region',
			],
		],
		'qrk_expedition_categories'        => [
			'label'        => 'Expedition Categories',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_expedition',
			],
		],
		'qrk_icons'                        => [
			'label'        => 'Icons',
			'hierarchical' => true,
			'post_types'   => [],
		],
		'qrk_inclusion_exclusion_category' => [
			'label'        => 'Category',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_inclusion_set',
				'qrk_exclusion_set',
			],
		],
		'qrk_spoken_languages'             => [
			'label'        => 'Spoken Languages',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_departure',
				'qrk_staff_member',
			],
		],
		'qrk_promotion_tags'               => [
			'label'        => 'Promotion Tags',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_marketing_promotion',
			],
		],
		'qrk_ship_categories'              => [
			'label'        => 'Ship Categories',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_ship',
			],
		],
		'qrk_tax_type'                     => [
			'label'        => 'Tax Type',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_itinerary',
			],
		],
		'sources_of_awareness'             => [
			'label'        => 'Sources of Awareness',
			'hierarchical' => true,
			'post_types'   => [],
		],
		'qrk_special_interests'            => [
			'label'        => 'Special Interests',
			'hierarchical' => true,
			'post_types'   => [],
		],
		'qrk_newspaper_editions'           => [
			'label'        => 'Newspaper Editions',
			'hierarchical' => true,
			'post_types'   => [],
		],
		'qrk_branding'                     => [
			'label'        => 'Branding',
			'hierarchical' => true,
			'post_types'   => [],
		],
		'qrk_season'                       => [
			'label'        => 'Seasons',
			'hierarchical' => true,
			'post_types'   => [
				'qrk_staff_member',
			],
		],
	];

	/**
	 * Register taxonomies.
	 */
	foreach ( $taxonomies_list as $slug => $item ) {
		$args = [
			'labels'            => [
				'name' => $item['label'],
			],
			'hierarchical'      => $item['hierarchical'],
			'public'            => true,
			'show_in_nav_menus' => false,
			'show_ui'           => true,
			'show_tagcloud'     => false,
			'show_admin_column' => true,
			'rewrite'           => true,
			'query_var'         => true,
			'show_in_rest'      => true,
		];
		register_taxonomy( $slug, $item['post_types'], $args );
	}
}

/**
 * Register post types.
 *
 * @return void
 */
function register_post_types() : void {
	/**
	 * Post types list.
	 *
	 * @var array<string, string[]> $post_types Post types list.
	 */
	$post_types = [
		'qrk_adventure_option' => [
			'labels'       => [
				'name'          => 'Adventure Options',
				'singular_name' => 'Adventure Option',
			],
			'public'       => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-location-alt',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'qrk_agent_page'       => [
			'labels'       => [
				'name'          => 'Agent Pages',
				'singular_name' => 'Agent Page',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-admin-users',
			'supports'     => [
				'title',
				'editor',
				'thumbnail',
			],
		],
		'qrk_agent_update'     => [
			'labels'       => [
				'name'          => 'Agent Updates',
				'singular_name' => 'Agent Update',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-edit-page',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'qrk_cabin_category'   => [
			'labels'       => [
				'name'          => 'Cabin Categories',
				'singular_name' => 'Cabin Category',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => 'edit.php?post_type=qrk_ship_deck',
			'menu_icon'    => 'dashicons-category',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'qrk_departure'        => [
			'labels'       => [
				'name'          => 'Departures',
				'singular_name' => 'Departure',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-airplane',
			'supports'     => [
				'title',
			],
		],
		'qrk_expedition'       => [
			'labels'       => [
				'name'          => 'Expeditions',
				'singular_name' => 'Expedition',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-palmtree',
			'supports'     => [
				'title',
				'editor',
			],
			'show_in_rest' => true,
		],
		'qrk_inclusion_set'    => [
			'labels'       => [
				'name'          => 'Inclusion Sets',
				'singular_name' => 'Inclusion Set',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-networking',
			'supports'     => [
				'title',
			],
		],
		'qrk_exclusion_set'    => [
			'labels'       => [
				'name'          => 'Exclusion Sets',
				'singular_name' => 'Exclusion Set',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-networking',
			'supports'     => [
				'title',
			],
		],
		'qrk_itinerary'        => [
			'labels'       => [
				'name'          => 'Itineraries',
				'singular_name' => 'Itinerary',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-list-view',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'qrk_itinerary_day'    => [
			'labels'       => [
				'name'          => 'Itinerary Days',
				'singular_name' => 'Itinerary Day',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-list-view',
			'supports'     => [
				'title',
			],
		],
		'qrk_offers'           => [
			'labels'       => [
				'name'          => 'Offers',
				'singular_name' => 'Offer',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-analytics',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'qrk_office'           => [
			'labels'       => [
				'name'          => 'Office',
				'singular_name' => 'Office',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-schedule',
			'supports'     => [
				'title',
			],
		],
		'qrk_photo_journal'    => [
			'labels'       => [
				'name'          => 'Photographic Journals',
				'singular_name' => 'Photographic Journal',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-embed-photo',
			'supports'     => [
				'title',
			],
		],
		'qrk_pre_post_trip'    => [
			'labels'       => [
				'name'          => 'Pre/Post-Trip Options',
				'singular_name' => 'Pre/Post-Trip Option',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => 'edit.php?post_type=qrk_expedition',
			'menu_icon'    => 'dashicons-tickets-alt',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'qrk_press_release'    => [
			'labels'       => [
				'name'          => 'Press Releases',
				'singular_name' => 'Press Release',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-welcome-write-blog',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'qrk_region'           => [
			'labels'       => [
				'name'          => 'Regions',
				'singular_name' => 'Region',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-admin-site',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'qrk_ship'             => [
			'labels'       => [
				'name'          => 'Ships',
				'singular_name' => 'Ship',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-sos',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'qrk_ship_deck'        => [
			'labels'       => [
				'name'          => 'Ship Deck',
				'singular_name' => 'Ship Deck',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-button',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'qrk_staff_member'     => [
			'labels'       => [
				'name'          => 'Staff Members',
				'singular_name' => 'Staff Member',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-groups',
			'supports'     => [
				'title',
				'editor',
				'thumbnail',
			],
		],
		'qrk_agreement'        => [
			'labels'       => [
				'name'          => 'Terms and Conditions / Policy Pages',
				'singular_name' => 'Terms and Conditions / Policy Page',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-media-document',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'qrk_testimonial'      => [
			'labels'       => [
				'name'          => 'Testimonials',
				'singular_name' => 'Testimonial',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-format-quote',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'qrk_port'             => [
			'labels'       => [
				'name'          => 'Ports',
				'singular_name' => 'Port',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => 'edit.php?post_type=qrk_itinerary',
			'menu_icon'    => 'dashicons-location',
			'supports'     => [
				'title',
				'editor',
			],
		],
	];

	/**
	 * Register post types.
	 */
	foreach ( $post_types as $slug => $args ) {
		register_post_type( $slug, $args );
	}
}
