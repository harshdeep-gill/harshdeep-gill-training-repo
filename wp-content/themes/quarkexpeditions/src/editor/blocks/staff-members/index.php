<?php
/**
 * Block: Staff Members.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\StaffMembers;

use WP_Query;

use function Quark\StaffMembers\get_cards_data;

use const Quark\StaffMembers\POST_TYPE as STAFF_MEMBER_POST_TYPE;
use const Quark\StaffMembers\DEPARTMENT_TAXONOMY as STAFF_MEMBER_DEPARTMENT_TAXONOMY;

const COMPONENT = 'parts.staff-members';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata(
		__DIR__,
		[
			'render_callback' => __NAMESPACE__ . '\\render',
		]
	);
}

/**
 * Render this block.
 *
 * @param mixed[] $attributes The block attributes.
 *
 * @return string The block markup.
 */
function render( array $attributes = [] ): string {
	// Build query args.
	$args = [
		'post_type'              => STAFF_MEMBER_POST_TYPE,
		'post_status'            => 'publish',
		'fields'                 => 'ids',
		'posts_per_page'         => $attributes['totalPosts'],
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'orderby'                => 'date',
		'order'                  => 'DESC',
	];

	// If the selection is manual, we need to check if we have IDs.
	if ( 'manual' === $attributes['selection'] ) {
		// Return empty if manual select, but no IDs were selected.
		if ( empty( $attributes['ids'] ) ) {
			return '';
		}

		// Set WP_Query args for manual selection.
		$args['post__in']       = $attributes['ids'];
		$args['orderby']        = 'post__in';
		$args['posts_per_page'] = count( $attributes['ids'] ); // phpcs:ignore
	} elseif ( 'recent' === $attributes['selection'] ) {
		// Set WP_Query args for department selection.
		$args['tax_query'] = [
			[
				'taxonomy' => STAFF_MEMBER_DEPARTMENT_TAXONOMY,
				'field'    => 'term_id',
				'terms'    => $attributes['departmentIds'],
			],
		];
	}

	// Query posts.
	$posts = new WP_Query( $args );

	// Get posts in array format of IDs.
	$post_ids = $posts->posts;

	// Check if we have posts.
	if ( empty( $post_ids ) ) {
		return '';
	}

	// Get blog post cards data.
	$cards_data = get_cards_data( array_map( 'absint', $post_ids ) );

	// Layout of the cards.
	$layout = $attributes['isCarousel'] ? 'carousel' : 'grid';

	// Return built component.
	return quark_get_component(
		COMPONENT,
		[
			'cards'  => $cards_data,
			'layout' => $layout,
		]
	);
}
