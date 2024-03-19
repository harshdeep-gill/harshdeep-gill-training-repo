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
		'accommodation_types'            => [
			'label'        => 'Accommodation Types',
			'hierarchical' => true,
			'post_types'   => [
				'ship',
			],
		],
		'adventure_options'              => [
			'label'        => 'Adventure Options',
			'hierarchical' => true,
			'post_types'   => [
				'adventure_option',
				'departure',
			],
		],
		'audiences'                      => [
			'label'        => 'Audiences',
			'hierarchical' => true,
			'post_types'   => [
				'm_p_landing',
				'marketing_promotion',
			],
		],
		'branding'                       => [
			'label'        => 'Branding',
			'hierarchical' => true,
			'post_types'   => [],
		],
		'cabin_classes'                  => [
			'label'        => 'Cabin Classes',
			'hierarchical' => true,
			'post_types'   => [
				'cabin_category',
			],
		],
		'charter_companies'              => [
			'label'        => 'Charter Companies',
			'hierarchical' => true,
			'post_types'   => [
				'departure',
			],
		],
		'departments'                    => [
			'label'        => 'Departments',
			'hierarchical' => true,
			'post_types'   => [
				'staff_member',
			],
		],
		'departure_destinations'         => [
			'label'        => 'Departure Destinations',
			'hierarchical' => true,
			'post_types'   => [],
		],
		'departure_locations'            => [
			'label'        => 'Departure Locations',
			'hierarchical' => true,
			'post_types'   => [
				'itinerary',
			],
		],
		'departure_staff_roles'          => [
			'label'        => 'Departure Staff Roles',
			'hierarchical' => true,
			'post_types'   => [
				'staff_member',
			],
		],
		'destinations'                   => [
			'label'        => 'Destinations',
			'hierarchical' => true,
			'post_types'   => [
				'adventure_option',
				'expedition',
				'm_p_landing',
				'region_landing_page',
				'marketing_promotion',
			],
		],
		'expedition_categories'          => [
			'label'        => 'Expedition Categories',
			'hierarchical' => true,
			'post_types'   => [
				'expedition',
			],
		],
		'expedition_types'               => [
			'label'        => 'Expedition Types',
			'hierarchical' => true,
			'post_types'   => [],
		],
		'icons'                          => [
			'label'        => 'Icons',
			'hierarchical' => true,
			'post_types'   => [],
		],
		'inclusion_exclusion_categories' => [
			'label'        => 'Inclusion Exclusion Categories',
			'hierarchical' => true,
			'post_types'   => [
				'inclusion_exclusion',
			],
		],
		'spoken_languages'               => [
			'label'        => 'Spoken Languages',
			'hierarchical' => true,
			'post_types'   => [
				'departure',
				'staff_member',
			],
		],
		'newspaper_editions'             => [
			'label'        => 'Newspaper Editions',
			'hierarchical' => true,
			'post_types'   => [],
		],
		'ports'                          => [
			'label'        => 'Ports',
			'hierarchical' => true,
			'post_types'   => [
				'itinerary',
			],
		],
		'promotion_tags'                 => [
			'label'        => 'Promotion Tags',
			'hierarchical' => true,
			'post_types'   => [
				'expedition',
				'm_p_landing',
				'marketing_promotion',
			],
		],
		'ship_categories'                => [
			'label'        => 'Ship Categories',
			'hierarchical' => true,
			'post_types'   => [
				'ship',
			],
		],
		'sources_of_awareness'           => [
			'label'        => 'Sources of Awareness',
			'hierarchical' => true,
			'post_types'   => [],
		],
		'special_interests'              => [
			'label'        => 'Special Interests',
			'hierarchical' => true,
			'post_types'   => [],
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
		'adventure_option'     => [
			'labels'       => [
				'name'          => 'Adventure Options',
				'singular_name' => 'Adventure Option',
			],
			'public'       => true,
			'supports'     => [
				'title',
				'editor',
			],
			'menu_icon'    => 'dashicons-location-alt',
			'show_in_rest' => true,
		],
		'agent_page'           => [
			'labels'       => [
				'name'          => 'Agent Pages',
				'singular_name' => 'Agent Page',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-admin-users',
			'supports'     => [
				'title',
				'editor',
				'thumbnail',
			],
		],
		'agent_update'         => [
			'labels'       => [
				'name'          => 'Agent Updates',
				'singular_name' => 'Agent Update',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-edit-page',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'cabin_category'       => [
			'labels'       => [
				'name'          => 'Cabin Categories',
				'singular_name' => 'Cabin Category',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-category',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'departure'            => [
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
		'expedition'           => [
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
		'inclusion_exclusion'  => [
			'labels'       => [
				'name'          => 'Inclusion / Exclusion Sets',
				'singular_name' => 'Inclusion / Exclusion Set',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-networking',
			'supports'     => [
				'title',
			],
		],
		'itinerary'            => [
			'labels'       => [
				'name'          => 'Iitineraries',
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
		'itinerary_day'        => [
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
		'landing_page'         => [
			'labels'       => [
				'name'          => 'Landing Pages',
				'singular_name' => 'Landing Page',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-id-alt',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'marketing_promotion'  => [
			'labels'       => [
				'name'          => 'Marketing Promotions (TO BE DEPRECATED)',
				'singular_name' => 'Marketing Promotion (TO BE DEPRECATED)',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-analytics',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'm_p_landing'          => [
			'labels'       => [
				'name'          => 'Marketing Promotion Landing Pages',
				'singular_name' => 'Marketing Promotion Landing Page',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-analytics',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'office'               => [
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
		'photographic_journal' => [
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
		'pre_post_trip_option' => [
			'labels'       => [
				'name'          => 'Pre/Post-Trip Options',
				'singular_name' => 'Pre/Post-Trip Option',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-tickets-alt',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'press_release'        => [
			'labels'       => [
				'name'          => 'Press Releases',
				'singular_name' => 'Press Release',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-welcome-write-blog',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'region_landing_page'  => [
			'labels'       => [
				'name'          => 'Region Landing Pages',
				'singular_name' => 'Region Landing Page',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-admin-site',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'ship'                 => [
			'labels'       => [
				'name'          => 'Ships',
				'singular_name' => 'Ship',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-sos',
			'supports'     => [
				'title',
				'editor',
			],
			'show_in_rest' => true,
		],
		'ship_deck'            => [
			'labels'       => [
				'name'          => 'Ship Deck',
				'singular_name' => 'Ship Deck',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-button',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'staff_insight'        => [
			'labels'       => [
				'name'          => 'Staff Insights',
				'singular_name' => 'Staff Insight',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-money',
			'supports'     => [
				'title',
			],
		],
		'staff_member'         => [
			'labels'       => [
				'name'          => 'Staff Members',
				'singular_name' => 'Staff Member',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-groups',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'agreement'            => [
			'labels'       => [
				'name'          => 'Terms and Conditions / Policy Pages',
				'singular_name' => 'Terms and Conditions / Policy Page',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-media-document',
			'supports'     => [
				'title',
				'editor',
			],
		],
		'testimonial'          => [
			'labels'       => [
				'name'          => 'Testimonials',
				'singular_name' => 'Testimonial',
			],
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-format-quote',
			'supports'     => [
				'title',
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
