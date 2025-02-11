<?php
/**
 * Namespace functions.
 *
 * @package quark-adventure-options
 */

namespace Quark\AdventureOptions;

use WP_Post;

const POST_TYPE                 = 'qrk_adventure_option';
const ADVENTURE_OPTION_CATEGORY = 'qrk_adventure_option_category';
const CACHE_KEY                 = POST_TYPE;
const CACHE_GROUP               = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type and taxonomies.
	add_action( 'init', __NAMESPACE__ . '\\register_adventure_option_post_type' );
	add_action( 'init', __NAMESPACE__ . '\\register_adventure_option_category_taxonomy' );

	// Opt into stuff.
	add_filter( 'qe_adventure_options_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );
	add_filter( 'qe_destination_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );

	// Cache Purge. Assigning non-standard priority to avoid race conditions with ACF.
	add_action( 'save_post', __NAMESPACE__ . '\\bust_post_cache', 11 );

	// Breadcrumbs.
	add_filter( 'travelopia_breadcrumbs_ancestors', __NAMESPACE__ . '\\breadcrumbs_ancestors' );

	// Add term meta keys to be translated while content sync.
	add_filter( 'qrk_translation_term_meta_keys', __NAMESPACE__ . '\\translate_term_meta_keys' );

	// Admin stuff.
	if ( is_admin() ) {
		// Custom fields.
		require_once __DIR__ . '/../custom-fields/adventure-option-category.php';
	}
}

/**
 * Register adventure option post type.
 *
 * @return void
 */
function register_adventure_option_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Adventure Options',
			'singular_name'      => 'Adventure Option',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Adventure Option',
			'edit_item'          => 'Edit Adventure Option',
			'new_item'           => 'New Adventure Option',
			'view_item'          => 'View Adventure Option',
			'search_items'       => 'Search Adventure Options',
			'not_found'          => 'No Adventure Options found',
			'not_found_in_trash' => 'No Adventure Options found in Trash',
			'parent_item_colon'  => 'Parent Adventure Option:',
			'menu_name'          => 'Adventure Options',
		],
		'public'              => true,
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-location-alt',
		'hierarchical'        => false,
		'supports'            => [
			'title',
			'editor',
			'excerpt',
			'thumbnail',
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
			'slug'       => 'adventure-options',
			'with_front' => false,
		],
		'capability_type'     => 'post',
		'template'            => [
			[
				'quark/hero',
				[
					'syncPostThumbnail' => true,
					'immersive'         => 'bottom',
					'contentOverlap'    => false,
				],
				[
					[
						'quark/breadcrumbs',
						[],
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
											'syncPostTitle' => true,
										],
									],
								],
							],
						],
					],
				],
			],
			[
				'core/paragraph',
				[],
			],
			[
				'quark/collage',
				[],
			],
		],
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
}

/**
 * Register Adventure Options taxonomy.
 *
 * @return void
 */
function register_adventure_option_category_taxonomy(): void {
	// Prepare labels.
	$labels = [
		'name'                       => 'Adventure Option Categories',
		'singular_name'              => 'Adventure Option Category',
		'search_items'               => 'Search Adventure Option Categories',
		'popular_items'              => 'Popular Adventure Option Categories',
		'all_items'                  => 'All Adventure Option Categories',
		'parent_item'                => 'Parent Adventure Option Category',
		'parent_item_colon'          => 'Parent Adventure Option Category:',
		'edit_item'                  => 'Edit Adventure Option Category',
		'update_item'                => 'Update Adventure Option Category',
		'add_new_item'               => 'Add New Adventure Option Category',
		'new_item_name'              => 'New Adventure Option Category',
		'separate_items_with_commas' => 'Separate Adventure Option Categories with commas',
		'add_or_remove_items'        => 'Add or remove Adventure Option Categories',
		'choose_from_most_used'      => 'Choose from the most used Adventure Option Categories',
		'menu_name'                  => 'Adventure Option Categories',
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
	register_taxonomy( ADVENTURE_OPTION_CATEGORY, (array) apply_filters( 'qe_adventure_options_taxonomy_post_types', [] ), $args );
}

/**
 * Opt into stuff.
 *
 * @param string[] $post_types Post types.
 *
 * @return string[]
 */
function opt_in( array $post_types = [] ): array {
	// Append Adventure Option post type for taxonomy.
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
	do_action( 'qe_adventure_option_post_cache_busted', $post_id );
}

/**
 * Get an Adventure Option.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     permalink: string,
 *     post_thumbnail: int,
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
			'permalink'       => $cached_value['permalink'] ?? '',
			'post_thumbnail'  => $cached_value['post_thumbnail'] ?? 0,
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
			'permalink'       => '',
			'post_thumbnail'  => 0,
			'post_meta'       => [],
			'post_taxonomies' => [],
		];
	}

	// Build data.
	$data = [
		'post'            => $post,
		'permalink'       => get_permalink( $post ) ?: '',
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
 * Get data for adventure options cards.
 *
 * @param int[] $post_ids Post IDs.
 *
 * @return array<mixed>{
 *    title: string,
 *    permalink: string,
 *    featured_image: int,
 *    excerpt: string,
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
		$post = get( $post_id );

		// Initialize $term_name.
		$term_name = '';

		// Check if terms are available.
		if (
			! empty( $post['post_taxonomies'][ ADVENTURE_OPTION_CATEGORY ] ) &&
			is_array( $post['post_taxonomies'][ ADVENTURE_OPTION_CATEGORY ] )
		) {
			// Get the term names and extract the first one.
			$term_names = wp_list_pluck( $post['post_taxonomies'][ ADVENTURE_OPTION_CATEGORY ], 'name' );
			$term_name  = array_shift( $term_names );
		}

		// Build post data.
		$post_data = [
			'title'          => $post['post']?->post_title ?? '',
			'permalink'      => $post['permalink'],
			'featured_image' => $post['post_thumbnail'],
			'excerpt'        => get_the_excerpt( $post['post']?->ID ),
			'term'           => $term_name,
		];

		// Add data to array.
		$data[] = $post_data;
	}

	// Return data.
	return $data;
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
		get_breadcrumbs_ancestors()
	);
}

/**
 * Get breadcrumbs ancestor.
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
	$press_release_archive_page = absint( get_option( 'options_adventure_options_page', 0 ) );

	// Initialize breadcrumbs.
	$breadcrumbs = [];

	// Get it's title and URL for breadcrumbs if it's set.
	if ( ! empty( $press_release_archive_page ) ) {
		$breadcrumbs[] = [
			'title' => get_the_title( $press_release_archive_page ),
			'url'   => strval( get_permalink( $press_release_archive_page ) ),
		];
	}

	// Return updated breadcrumbs.
	return $breadcrumbs;
}

/**
 * Translate term meta keys.
 *
 * @param array<string, string> $meta_keys Meta keys.
 *
 * @return array<string, string|string[]>
 */
function translate_term_meta_keys( array $meta_keys = [] ): array {
	// Meta keys for translation.
	$extra_keys = [
		'image' => 'attachment',
		'icon'  => 'attachment',
	];

	// Return meta keys to be translated.
	return array_merge( $meta_keys, $extra_keys );
}
