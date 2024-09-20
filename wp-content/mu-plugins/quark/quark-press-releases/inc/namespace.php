<?php
/**
 * Namespace functions.
 *
 * @package quark-press-releases
 */

namespace Quark\PressReleases;

use WP_Post;

use function Travelopia\Breadcrumbs\get_breadcrumbs;

const POST_TYPE = 'qrk_press_release';

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Register post type.
	add_action( 'init', __NAMESPACE__ . '\\register_press_release_post_type' );

	// Add date to press release permalink.
	add_filter( 'post_type_link', __NAMESPACE__ . '\\update_permalink', 10, 2 );

	// Breadcrumbs.
	add_filter( 'travelopia_breadcrumbs_ancestors', __NAMESPACE__ . '\\breadcrumbs_ancestors' );
}

/**
 * Register press release post type.
 *
 * @return void
 */
function register_press_release_post_type(): void {
	// Post type arguments.
	$args = [
		'labels'              => [
			'name'               => 'Press Release',
			'singular_name'      => 'Press Release',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Press Release',
			'edit_item'          => 'Edit Press Release',
			'new_item'           => 'New Press Release',
			'view_item'          => 'View Press Release',
			'search_items'       => 'Search Press Releases',
			'not_found'          => 'No Press Releases found',
			'not_found_in_trash' => 'No Press Releases found in Trash',
			'parent_item_colon'  => 'Parent Press Releasee:',
			'menu_name'          => 'Press Releases',
		],
		'hierarchical'        => false,
		'supports'            => [ 'title', 'editor', 'excerpt', 'revisions' ],
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_icon'           => 'dashicons-welcome-write-blog',
		'show_in_nav_menus'   => false,
		'publicly_queryable'  => true,
		'exclude_from_search' => true,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => [
			'slug'       => 'press-releases/%year%/%monthnum%',
			'with_front' => false,
		],
		'capability_type'     => 'post',
		'show_in_rest'        => true,
		'menu_position'       => 5,
		'template'            => [
			[
				'core/heading',
				[
					'level'       => 5,
					'placeholder' => 'Place - Date',
				],
			],
			[
				'core/paragraph',
				[
					'placeholder' => 'Press Release Content here....',
				],
			],
		],
	];

	// Register post type.
	register_post_type( POST_TYPE, $args );
}

/**
 * Add date to press release permalink.
 *
 * @param string       $post_link Post link.
 * @param WP_Post|null $post Post object.
 *
 * @return string
 */
function update_permalink( string $post_link = '', WP_Post $post = null ): string {
	// Check if post type is press release.
	if ( $post instanceof WP_Post && POST_TYPE === $post->post_type ) {
		$year      = gmdate( 'Y', absint( strtotime( $post->post_date_gmt ) ) );
		$month     = gmdate( 'm', absint( strtotime( $post->post_date_gmt ) ) );
		$post_link = str_replace( '%year%', strval( $year ), $post_link );
		$post_link = str_replace( '%monthnum%', strval( $month ), $post_link );
	}

	// Return post link.
	return $post_link;
}

/**
 * Get a Press Release.
 *
 * @param int $post_id Post ID.
 *
 * @return array{
 *     post: WP_Post|null,
 *     permalink: string,
 * }
 */
function get( int $post_id = 0 ): array {
	// Get post.
	$post = get_post( $post_id );

	// Return empty array fields if post type does not match or not an instance of WP_Post.
	if ( ! $post instanceof WP_Post || POST_TYPE !== $post->post_type ) {
		return [
			'post'      => null,
			'permalink' => '',
		];
	}

	// Return post data.
	return [
		'post'      => $post,
		'permalink' => strval( get_permalink( $post ) ? : '' ),
	];
}

/**
 * Get press release cards data.
 *
 * @param int[] $post_ids Post IDs.
 *
 * @return array{}|array{
 *     id: int,
 *     title: string,
 *     description: string,
 *     permalink: string,
 * }[] Press release cards data.
 */
function get_cards_data( array $post_ids = [] ): array {
	// Return if empty post IDs.
	if ( empty( $post_ids ) ) {
		return [];
	}

	// Initialize press release cards.
	$press_release_cards = [];

	// Loop through post IDs.
	foreach ( $post_ids as $post_id ) {
		// Get press release.
		$press_release = get( $post_id );

		// Skip if post is not a press release.
		if ( empty( $press_release['post'] ) ) {
			continue;
		}

		// Add press release card.
		$press_release_cards[] = [
			'id'          => $press_release['post']->ID,
			'title'       => $press_release['post']->post_title,
			'description' => strval( apply_filters( 'the_content', $press_release['post']->post_excerpt ) ),
			'permalink'   => $press_release['permalink'],
		];
	}

	// Return press release cards.
	return $press_release_cards;
}

/**
 * Breadcrumbs ancestors for this post type.
 *
 * @param mixed[] $breadcrumbs Breadcrumbs.
 *
 * @return array{}|array{
 *     array{
 *         title: string,
 *         url: string,
 *     }
 * }
 */
function breadcrumbs_ancestors( array $breadcrumbs = [] ): array {
	// Check if current query is for this post type.
	if ( ! is_singular( POST_TYPE ) && ! is_author() && ! is_category() ) {
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
	$press_release_archive_page = absint( get_option( 'options_press_releases_page', 0 ) );

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
