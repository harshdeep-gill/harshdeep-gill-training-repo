<?php
/**
 * Namespace functions.
 *
 * @package quark-staff-members
 */

namespace Quark\StaffMembers;

use WP_Post;

const POST_TYPE                     = 'qrk_staff_member';
const DEPARTMENT_TAXONOMY           = 'qrk_department';
const DEPARTURE_STAFF_ROLE_TAXONOMY = 'qrk_staff_role';
const SEASON_TAXONOMY               = 'qrk_season';
const CACHE_KEY                     = POST_TYPE;
const CACHE_GROUP                   = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type and taxonomy.
	add_action( 'init', __NAMESPACE__ . '\\register_staff_member_post_type' );
	add_action( 'init', __NAMESPACE__ . '\\register_department_taxonomy' );
	add_action( 'init', __NAMESPACE__ . '\\register_staff_role_taxonomy' );
	add_action( 'init', __NAMESPACE__ . '\\register_season_taxonomy' );

	// Opt into stuff.
	add_filter( 'qe_department_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_staff_role_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_season_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_spoken_language_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );

	// Other hooks. Assigning non-standard priority to avoid race conditions with ACF.
	add_action( 'save_post', __NAMESPACE__ . '\\bust_post_cache', 11 );

	// Breadcrumbs.
	add_filter( 'travelopia_breadcrumbs_ancestors', __NAMESPACE__ . '\\breadcrumbs_ancestors' );

	// SEO.
	add_filter( 'travelopia_seo_structured_data_schema', __NAMESPACE__ . '\\seo_structured_data' );

	// Add meta keys to be translated while content sync.
	add_filter( 'qrk_translation_meta_keys', __NAMESPACE__ . '\\translate_meta_keys' );

	// Admin stuff.
	if ( is_admin() ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/staff-members.php';
	}
}

/**
 * Register Staff Member post type.
 *
 * @return void
 */
function register_staff_member_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Staff Members',
			'singular_name'      => 'Staff Member',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Staff Member',
			'edit_item'          => 'Edit Staff Member',
			'new_item'           => 'New Staff Member',
			'view_item'          => 'View Staff Member',
			'search_items'       => 'Search Staff Members',
			'not_found'          => 'No Staff Members found',
			'not_found_in_trash' => 'No Staff Members found in Trash',
			'parent_item_colon'  => 'Parent Staff Member:',
			'menu_name'          => 'Staff Members',
		],
		'public'              => true,
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-groups',
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
			'slug'       => 'staff',
			'with_front' => false,
		],
		'capability_type'     => 'post',
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
}

/**
 * Register Department taxonomy.
 *
 * @return void
 */
function register_department_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Departments',
		'singular_name'              => 'Department',
		'search_items'               => 'Search Departments',
		'popular_items'              => 'Popular Departments',
		'all_items'                  => 'All Departments',
		'parent_item'                => 'Parent Department',
		'parent_item_colon'          => 'Parent Department:',
		'edit_item'                  => 'Edit Department',
		'update_item'                => 'Update Department',
		'add_new_item'               => 'Add New Department',
		'new_item_name'              => 'New Department',
		'separate_items_with_commas' => 'Separate Departments with commas',
		'add_or_remove_items'        => 'Add or remove Departments',
		'choose_from_most_used'      => 'Choose from the most used Departments',
		'menu_name'                  => 'Departments',
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
	register_taxonomy( DEPARTMENT_TAXONOMY, (array) apply_filters( 'qe_department_taxonomy_post_types', [] ), $args );
}

/**
 * Register Departure Staff Role taxonomy.
 *
 * @return void
 */
function register_staff_role_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Staff Roles',
		'singular_name'              => 'Staff Role',
		'search_items'               => 'Search Staff Roles',
		'popular_items'              => 'Popular Staff Roles',
		'all_items'                  => 'All Staff Roles',
		'parent_item'                => 'Parent Staff Role',
		'parent_item_colon'          => 'Parent Staff Role:',
		'edit_item'                  => 'Edit Staff Role',
		'update_item'                => 'Update Staff Role',
		'add_new_item'               => 'Add New Staff Role',
		'new_item_name'              => 'New Staff Role',
		'separate_items_with_commas' => 'Separate Staff Roles with commas',
		'add_or_remove_items'        => 'Add or remove Staff Roles',
		'choose_from_most_used'      => 'Choose from the most used Staff Roles',
		'menu_name'                  => 'Staff Roles',
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
	register_taxonomy( DEPARTURE_STAFF_ROLE_TAXONOMY, (array) apply_filters( 'qe_staff_role_taxonomy_post_types', [] ), $args );
}

/**
 * Register Seasons taxonomy.
 *
 * @return void
 */
function register_season_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Seasons',
		'singular_name'              => 'Season',
		'search_items'               => 'Search Seasons',
		'popular_items'              => 'Popular Seasons',
		'all_items'                  => 'All Seasons',
		'parent_item'                => 'Parent Season',
		'parent_item_colon'          => 'Parent Season:',
		'edit_item'                  => 'Edit Season',
		'update_item'                => 'Update Season',
		'add_new_item'               => 'Add New Season',
		'new_item_name'              => 'New Season',
		'separate_items_with_commas' => 'Separate Seasons with commas',
		'add_or_remove_items'        => 'Add or remove Seasons',
		'choose_from_most_used'      => 'Choose from the most used Seasons',
		'menu_name'                  => 'Season',
	];

	// Prepare args for registering taxonomy.
	$args = [
		'labels'            => $labels,
		'public'            => false,
		'show_in_nav_menus' => false,
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
	register_taxonomy( SEASON_TAXONOMY, (array) apply_filters( 'qe_season_taxonomy_post_types', [] ), $args );
}

/**
 * Opt into stuff.
 *
 * @param string[] $post_types Post types.
 *
 * @return string[]
 */
function opt_in( array $post_types = [] ): array {
	// Append staff member post type for taxonomy.
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

	// Bail early if post type does not match.
	if ( POST_TYPE !== $post_type ) {
		return;
	}

	// Clear cache for this post.
	wp_cache_delete( CACHE_KEY . "_$post_id", CACHE_GROUP );

	// Trigger action to clear cache for this post.
	do_action( 'qe_staff_member_cache_busted', $post_id );
}

/**
 * Get a Staff member post.
 *
 * @param int $post_id Staff Member Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     permalink: string,
 *     post_thumbnail: int,
 *     post_meta: mixed[],
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
			'post_thumbnail'  => $cached_value['post_thumbnail'] ?? 0,
			'post_meta'       => $cached_value['post_meta'] ?? [],
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
			'post_thumbnail'  => 0,
			'post_meta'       => [],
			'post_taxonomies' => [],
		];
	}

	// Build data.
	$data = [
		'post'            => $post,
		'permalink'       => strval( get_permalink( $post ) ? : '' ),
		'post_thumbnail'  => get_post_thumbnail_id( $post ) ?: 0,
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
 * Build structured data for schema.
 *
 * @param mixed[] $schema All schema data.
 *
 * @return mixed[]
 */
function seo_structured_data( array $schema = [] ): array {
	// Check if this is a staff member page.
	if ( ! is_singular( POST_TYPE ) ) {
		return $schema;
	}

	// Get and insert the schema.
	$schema[] = get_structured_data( absint( get_the_ID() ) );

	// Return the schema.
	return $schema;
}

/**
 * Get structured data for this post type.
 *
 * @param int $post_id Post ID.
 *
 * @return array{}|array{
 *    "@context": string,
 *    "@type": string,
 *    "name": string,
 *    "jobTitle": string,
 *    "affiliation": array{
 *        "@type": string,
 *        "name": string,
 *    },
 *   "url": string,
 * }
 */
function get_structured_data( int $post_id = 0 ): array {
	// Get post data.
	$post = get( $post_id );

	// Return early if post couldn't be fetched or not of this post type.
	if ( ! $post['post'] instanceof WP_Post || POST_TYPE !== $post['post']->post_type ) {
		return [];
	}

	// Get name.
	$name = sprintf( '%s %s', strval( $post['post_meta']['first_name'] ), strval( $post['post_meta']['last_name'] ) );

	// Get Job Title.
	$role = get_roles( $post['post']->ID );
	$role = ! empty( $role ) ? $role[0]['name'] : '';

	// Return Schema.
	return [
		'@context'    => 'https://schema.org',
		'@type'       => 'Person',
		'name'        => $name,
		'jobTitle'    => $role,
		'affiliation' => [
			'@type' => 'Organization',
			'name'  => 'Employee',
		],
		'url'         => $post['permalink'],
	];
}

/**
 * Get Department.
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
function get_department( int $post_id = 0 ): array {
	// Get post ID.
	$post = get( $post_id );

	// If post not found then return empty array.
	if ( ! $post['post'] instanceof WP_Post ) {
		return [];
	}

	// If there is no season, return empty array.
	if (
		empty( $post['post_taxonomies'][ DEPARTMENT_TAXONOMY ] ) ||
		! is_array( $post['post_taxonomies'][ DEPARTMENT_TAXONOMY ] ) ||
		empty( $post['post_taxonomies'][ DEPARTMENT_TAXONOMY ][0] ) ||
		empty( $post['post_taxonomies'][ DEPARTMENT_TAXONOMY ][0]['term_id'] )
	) {
		return [];
	}

	// Return season data.
	return $post['post_taxonomies'][ DEPARTMENT_TAXONOMY ][0];
}

/**
 * Get Departments.
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
 * }[] Season data.
 */
function get_departments( int $post_id = 0 ): array {
	// Get post ID.
	$post = get( $post_id );

	// If post not found then return empty array.
	if ( ! $post['post'] instanceof WP_Post ) {
		return [];
	}

	// If there is no season, return empty array.
	if (
		empty( $post['post_taxonomies'][ DEPARTMENT_TAXONOMY ] ) ||
		! is_array( $post['post_taxonomies'][ DEPARTMENT_TAXONOMY ] )
	) {
		return [];
	}

	// Return season data.
	return $post['post_taxonomies'][ DEPARTMENT_TAXONOMY ];
}

/**
 * Get Departments.
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
 * }[] Season data.
 */
function get_roles( int $post_id = 0 ): array {
	// Get post ID.
	$post = get( $post_id );

	// If post not found then return empty array.
	if ( ! $post['post'] instanceof WP_Post ) {
		return [];
	}

	// If there is no season, return empty array.
	if (
		empty( $post['post_taxonomies'][ DEPARTURE_STAFF_ROLE_TAXONOMY ] ) ||
		! is_array( $post['post_taxonomies'][ DEPARTURE_STAFF_ROLE_TAXONOMY ] )
	) {
		return [];
	}

	// Return season data.
	return $post['post_taxonomies'][ DEPARTURE_STAFF_ROLE_TAXONOMY ];
}

/**
 * Get data for adventure options cards.
 *
 * @param int[] $post_ids Post IDs.
 *
 * @return array<mixed>{
 *    title: string,
 *    permalink: string,
 *    featured_image: int,
 *    role: string,
 *    season: string,
 * }[]
 */
function get_cards_data( array $post_ids = [] ): array {
	// Check if post ids exist.
	if ( empty( $post_ids ) ) {
		return [];
	}

	// Initialize data.
	$data = [];

	// Loop through the post ids.
	foreach ( $post_ids as $post_id ) {
		// Get post data.
		$post_data = get( $post_id );

		// Check if we have post data.
		if ( empty( $post_data['post'] ) ) {
			continue;
		}

		// Get post meta.
		$post_meta = $post_data['post_meta'];

		// Get post taxonomies.
		$post_taxonomies = $post_data['post_taxonomies'];

		// Initialize season.
		$season = '';

		// Check if we have season taxonomy.
		if ( ! empty( $post_taxonomies[ SEASON_TAXONOMY ] ) && is_array( $post_taxonomies[ SEASON_TAXONOMY ] ) ) {
			// Get season name.
			$season = $post_taxonomies[ SEASON_TAXONOMY ][0]['name'];
		}

		// Get featured image id.
		$featured_image = $post_data['post_thumbnail'];

		// Build data.
		$data[] = [
			'title'          => $post_data['post']->post_title,
			'permalink'      => $post_data['permalink'],
			'featured_image' => $featured_image,
			'role'           => $post_meta['job_title'] ?? '',
			'season'         => $season,
		];
	}

	// Return data.
	return $data;
}

/**
 * Breadcrumbs ancestors for this post type.
 *
 * @param mixed[] $breadcrumbs Breadcrumbs.
 *
 * @return array{}|array{
 *    array{
 *       title: string,
 *       url: string,
 *    }
 * }
 */
function breadcrumbs_ancestors( array $breadcrumbs = [] ): array {
	// Check if current query is for this post type.
	if ( ! is_singular( POST_TYPE ) ) {
		return $breadcrumbs;
	}

	// Return the breadcrumbs ancestors.
	return array_merge(
		$breadcrumbs,
		get_breadcrumbs_ancestors()
	);
}

/**
 * Get breadcrumbs ancestors for this post type.
 *
 * @return array{}|array{
 *     array{
 *         title: string,
 *         url: string,
 *     }
 * }
 */
function get_breadcrumbs_ancestors(): array {
	// Get archive page.
	$archive_page_id = absint( get_option( 'options_staff_members_page', 0 ) );

	// Initialize breadcrumbs.
	$breadcrumbs = [];

	// Get it's title and URL for breadcrumbs if it's set.
	if ( ! empty( $archive_page_id ) ) {
		$breadcrumbs[] = [
			'title' => get_the_title( $archive_page_id ),
			'url'   => strval( get_permalink( $archive_page_id ) ),
		];
	}

	// Return updated breadcrumbs.
	return $breadcrumbs;
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
		'job_title'            => 'string',
		'first_name'           => 'string',
		'last_name'            => 'string',
		'hometown'             => 'string',
		'countries_travelled'  => 'post',
		'favorite_destination' => 'post',
	];

	// Return meta keys to be translated.
	return array_merge( $meta_keys, $extra_keys );
}
