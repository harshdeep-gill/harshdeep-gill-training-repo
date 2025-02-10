<?php
/**
 * Block: Staff Members.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\StaffMembers;

use WP_Post;
use WP_Query;

use function Quark\Core\get_first_pagination_link;
use function Quark\Core\get_last_pagination_link;
use function Quark\Core\get_pagination_links;
use function Quark\StaffMembers\get as get_staff_member;
use function Quark\StaffMembers\get_cards_data;
use function Quark\StaffMembers\get_department;
use function Quark\Core\is_block_editor;

use const Quark\StaffMembers\POST_TYPE as STAFF_MEMBER_POST_TYPE;
use const Quark\StaffMembers\DEPARTMENT_TAXONOMY as STAFF_MEMBER_DEPARTMENT_TAXONOMY;

const COMPONENT  = 'parts.staff-members';
const BLOCK_NAME = 'quark/staff-members';

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

	// Add block attributes to translate.
	add_filter( 'qrk_translation_block_attributes', __NAMESPACE__ . '\\block_attributes_to_translate' );
}

/**
 * Render this block.
 *
 * @param mixed[] $attributes The block attributes.
 *
 * @return string The block markup.
 */
function render( array $attributes = [] ): string {
	// Get the current staff member.
	$current_staff_member = get_staff_member();

	// Get the current page.
	$current_page = get_query_var( 'paged' ) ?: 1;

	// Build query args.
	$args = [
		'post_type'              => STAFF_MEMBER_POST_TYPE,
		'post_status'            => 'publish',
		'fields'                 => 'ids',
		'posts_per_page'         => $attributes['totalPosts'],
		'no_found_rows'          => ! $attributes['hasPagination'],
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'orderby'                => 'title',
		'order'                  => 'ASC',
		'paged'                  => $current_page,
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
		$args['posts_per_page'] = count( $attributes['ids'] );
	} elseif ( 'recent' === $attributes['selection'] ) {
		// Set WP_Query args for department selection.
		$args['tax_query'] = [
			[
				'taxonomy' => STAFF_MEMBER_DEPARTMENT_TAXONOMY,
				'field'    => 'term_id',
				'terms'    => $attributes['departmentIds'],
			],
		];
	} elseif ( 'auto' === $attributes['selection'] ) {
		// Check if we have a staff member.
		if ( $current_staff_member['post'] instanceof WP_Post ) {
			$department = get_department( $current_staff_member['post']->ID );

			// Check if we have a department.
			if ( ! empty( $department ) ) {
				// Set WP_Query args for department selection.
				$args['tax_query'] = [
					[
						'taxonomy' => STAFF_MEMBER_DEPARTMENT_TAXONOMY,
						'field'    => 'term_id',
						'terms'    => $department['term_id'],
					],
				];

				// Set the number of posts to show. Adding one extra to remove the current staff member.
				$args['posts_per_page'] = 4;
			}
		}
	}

	// Query posts.
	$posts = new WP_Query( $args );

	// Get posts in array format of IDs.
	$post_ids = $posts->posts;

	// Check if we have posts.
	if ( 'auto' === $attributes['selection'] && $current_staff_member['post'] instanceof WP_Post ) {
		// Get the key of the current staff member.
		$key = array_search( $current_staff_member['post']->ID, $post_ids, true );

		// Remove the current staff member from the list.
		if ( false !== $key ) {
			unset( $post_ids[ $key ] );
		}

		// Make sure we have 3 posts.
		if ( 4 <= count( $post_ids ) ) {
			array_pop( $post_ids );
		}
	}

	// Check if we have posts.
	if ( empty( $post_ids ) ) {
		return '';
	}

	// Get blog post cards data.
	$cards_data = get_cards_data( array_map( 'absint', $post_ids ) );

	// Layout of the cards.
	$layout = $attributes['isCarousel'] ? 'carousel' : 'grid';

	// Initialize pagination.
	$pagination = '';

	// Check if we have cards data pagination.
	if ( ! empty( $attributes['hasPagination'] ) && ! is_block_editor() ) {
		$pagination = get_pagination_links(
			[
				'query' => $posts,
			]
		);
	}

	// Return built component.
	return quark_get_component(
		COMPONENT,
		[
			'cards'           => $cards_data,
			'layout'          => $layout,
			'showSeason'      => $attributes['showSeason'],
			'showTitle'       => $attributes['showTitle'],
			'showRole'        => $attributes['showRole'],
			'showCta'         => $attributes['showCta'],
			'pagination'      => $pagination,
			'current_page'    => $current_page,
			'total_pages'     => $posts->max_num_pages,
			'first_page_link' => 1 !== $current_page ? get_first_pagination_link() : '',
			'last_page_link'  => $current_page !== $posts->max_num_pages ? get_last_pagination_link( [ 'total' => $posts->max_num_pages ] ) : '',
		]
	);
}

/**
 * Block attributes that need to be translatable.
 *
 * @param mixed[] $blocks_and_attributes Blocks and attributes.
 *
 * @return mixed[]
 */
function block_attributes_to_translate( array $blocks_and_attributes = [] ): array {
	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME ] = [
		'post_id' => [ 'ids' ],
		'term_id' => [ 'departmentIds' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
