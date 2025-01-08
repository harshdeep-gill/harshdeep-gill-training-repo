<?php
/**
 * Namespace functions.
 *
 * @package quark-regions
 */

namespace Quark\Regions;

use WP;
use WP_Post;

const POST_TYPE   = 'qrk_region';
const CACHE_KEY   = POST_TYPE;
const CACHE_GROUP = POST_TYPE;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Post type.
	add_action( 'init', __NAMESPACE__ . '\\register_region_post_type' );

	// Opt into stuff.
	add_filter( 'qe_destination_taxonomy_post_types', __NAMESPACE__ . '\\opt_in' );

	// Permalink and rewrite rules.
	add_action( 'init', __NAMESPACE__ . '\\rewrite_rules' );
	add_filter( 'post_type_link', __NAMESPACE__ . '\\get_custom_permalink', 10, 2 );

	// Other hooks. Assigning non-standard priority to avoid race conditions with ACF.
	add_action( 'save_post', __NAMESPACE__ . '\\bust_post_cache', 11 );
}

/**
 * Register Regions post type.
 *
 * @return void
 */
function register_region_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Regions',
			'singular_name'      => 'Region',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Region',
			'edit_item'          => 'Edit Region',
			'new_item'           => 'New Region',
			'view_item'          => 'View Region',
			'search_items'       => 'Search Regions',
			'not_found'          => 'No Regions found',
			'not_found_in_trash' => 'No Regions found in Trash',
			'parent_item_colon'  => 'Parent Region:',
			'menu_name'          => 'Regions',
		],
		'public'              => true,
		'show_in_rest'        => true,
		'menu_icon'           => 'dashicons-admin-site',
		'hierarchical'        => true,
		'supports'            => [
			'title',
			'editor',
			'page-attributes',
			'excerpt',
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
			'slug'       => 'regions',
			'with_front' => false,
		],
		'capability_type'     => 'post',
		'template'            => [
			[
				'quark/hero',
				[
					'immersive'      => 'bottom',
					'overlapContent' => false,
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
									[
										'quark/hero-subtitle',
										[],
									],
									[
										'quark/button',
										[
											'btnText' => 'View Expeditions',
											'url'     => [
												'url'  => '#expeditions',
												'text' => 'View Expeditions',
											],
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
										'url'  => 'overview',
										'text' => 'Overview',
									],
								],
							],
							[
								'quark/secondary-navigation-item',
								[
									'title' => 'Destination Highlights',
									'url'   => [
										'url'  => 'destination-highlights',
										'text' => 'Destination Highlights',
									],
								],
							],
							[
								'quark/secondary-navigation-item',
								[
									'title' => 'Top Things to See',
									'url'   => [
										'url'  => 'top-things-to-see',
										'text' => 'Top Things to See',
									],
								],
							],
							[
								'quark/secondary-navigation-item',
								[
									'title' => 'When to Go',
									'url'   => [
										'url'  => 'when-to-go',
										'text' => 'When to Go',
									],
								],
							],
							[
								'quark/secondary-navigation-item',
								[
									'title' => 'Testimonials',
									'url'   => [
										'url'  => 'testimonials',
										'text' => 'Testimonials',
									],
								],
							],
							[
								'quark/secondary-navigation-item',
								[
									'title' => 'Expeditions',
									'url'   => [
										'url'  => 'expeditions',
										'text' => 'Expeditions',
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
					'title'          => 'Overview',
					'titleAlignment' => 'left',
					'headingLevel'   => '2',
					'hasDescription' => false,
					'anchor'         => 'overview',
				],
				[
					[
						'core/paragraph',
						[],
					],
					[
						'quark/specifications',
						[
							'title' => 'Destination Specifications',
						],
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => 'Destination Highlights',
					'titleAlignment' => 'left',
					'headingLevel'   => '2',
					'hasDescription' => false,
					'anchor'         => 'destination-highlights',
				],
				[
					[
						'quark/media-description-cards',
						[],
					],
					[
						'quark/fancy-video',
						[],
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => 'Top Things to See',
					'titleAlignment' => 'left',
					'headingLevel'   => '2',
					'hasDescription' => false,
					'anchor'         => 'top-things-to-see',
				],
				[
					[
						'quark/media-description-cards',
						[],
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => 'Points of Interest',
					'titleAlignment' => 'left',
					'headingLevel'   => '2',
					'hasDescription' => false,
					'anchor'         => 'points-of-interest',
				],
				[
					[
						'quark/media-text-cta-carousel',
						[],
						[
							[
								'quark/media-text-cta',
								[],
								[
									[
										'core/heading',
										[
											'level' => 3,
										],
									],
									[
										'core/paragraph',
										[],
									],
								],
							],
							[
								'quark/media-text-cta',
								[
									'mediaAlignment' => 'right',
								],
								[
									[
										'core/heading',
										[
											'level' => 3,
										],
									],
									[
										'core/paragraph',
										[],
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
					'title'          => 'When to Go',
					'titleAlignment' => 'left',
					'headingLevel'   => '2',
					'hasDescription' => false,
					'anchor'         => 'when-to-go',
				],
				[
					[
						'quark/icon-info-grid',
						[],
						[
							[
								'quark/icon-info-grid-item',
								[],
							],
						],
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => 'Special Insights from Our Guests',
					'titleAlignment' => 'left',
					'headingLevel'   => '2',
					'hasDescription' => false,
					'anchor'         => 'testimonials',
				],
				[
					[
						'quark/review-cards',
						[],
					],
				],
			],
			[
				'quark/section',
				[
					'title'          => 'Expeditions to This Region',
					'titleAlignment' => 'left',
					'headingLevel'   => '2',
					'hasDescription' => false,
					'anchor'         => 'expeditions',
				],
				[
					[
						'quark/expeditions',
						[],
					],
				],
			],
			[
				'quark/cta-banner',
				[],
			],
		],
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
}

/**
 * Opt into stuff.
 *
 * @param string[] $post_types Post types.
 *
 * @return string[]
 */
function opt_in( array $post_types = [] ): array {
	// Append this post type for taxonomy.
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
	do_action( 'qe_region_cache_busted', $post_id );
}

/**
 * Get a Region page.
 *
 * @param int $page_id Region Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     permalink: string,
 * }
 */
function get( int $page_id = 0 ): array {
	// Get post ID.
	if ( 0 === $page_id ) {
		$page_id = absint( get_the_ID() );
	}

	// Check for cached version.
	$cache_key    = CACHE_KEY . "_$page_id";
	$cached_value = wp_cache_get( $cache_key, CACHE_GROUP );

	// Check for cached value.
	if ( is_array( $cached_value ) && ! empty( $cached_value['post'] ) && $cached_value['post'] instanceof WP_Post ) {
		return [
			'post'      => $cached_value['post'],
			'permalink' => $cached_value['permalink'] ?? '',
		];
	}

	// Get post.
	$page = get_post( $page_id );

	// Return empty array fields if post type does not match or not an instance of WP_Post.
	if ( ! $page instanceof WP_Post || POST_TYPE !== $page->post_type ) {
		return [
			'post'      => null,
			'permalink' => '',
		];
	}

	// Build data.
	$data = [
		'post'      => $page,
		'permalink' => strval( get_permalink( $page ) ? : '' ),
	];

	// Set cache and return data.
	wp_cache_set( $cache_key, $data, CACHE_GROUP );

	// Return data.
	return $data;
}

/**
 * Rewrite rules for this post type.
 *
 * @return void
 */
function rewrite_rules(): void {
	// Match URLs with one level post.
	add_rewrite_rule(
		'^([^/]+)/?$',
		'index.php?' . POST_TYPE . '=$matches[1]'
	);

	// Match URLs with one or more slashes for parent-child relations.
	add_rewrite_rule(
		'^([^/]+)/([^/]+)/?$',
		'index.php?' . POST_TYPE . '=$matches[1]/$matches[2]'
	);
}

/**
 * Get custom permalink for this post type.
 *
 * @param string       $permalink Original permalink.
 * @param WP_Post|null $post      Post object.
 *
 * @return string
 */
function get_custom_permalink( string $permalink = '', WP_Post $post = null ): string {
	// Return permalink if post is not a post type.
	if ( ! $post instanceof WP_Post || POST_TYPE !== $post->post_type ) {
		return $permalink;
	}

	// Construct permalink.
	return str_replace( '/regions', '', $permalink );
}
