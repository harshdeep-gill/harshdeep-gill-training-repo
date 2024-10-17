<?php
/**
 * Block Name: Press Releases.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\PressReleases;

use WP_Block;
use WP_Query;

use function Quark\Core\get_pagination_links;
use function Quark\Core\get_first_pagination_link;
use function Quark\Core\get_last_pagination_link;
use function Quark\PressReleases\get_cards_data;

use const Quark\PressReleases\POST_TYPE;

const COMPONENT = 'parts.press-releases';

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
 * @param mixed[]       $attributes The block attributes.
 * @param string        $content The block content.
 * @param WP_Block|null $block The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Get current page.
	$current_page = get_query_var( 'paged' ) ?: 1;

	// Build query arguments.
	$arguments = [
		'post_type'      => POST_TYPE,
		'posts_per_page' => 8,
		'orderby'        => 'date',
		'fields'         => 'ids',
		'order'          => 'DESC',
		'post_status'    => 'publish',
		'paged'          => $current_page,
	];

	// Get Press Releases.
	$press_releases = new WP_Query( $arguments );

	// Check for Press Releases.
	if ( ! $press_releases->have_posts() ) {
		return '';
	}

	// Get pagination.
	$pagination = get_pagination_links(
		[
			'query' => $press_releases,
		]
	);

	// Build component attributes.
	$component_attributes = [
		'cards'                => get_cards_data( array_map( 'absint', $press_releases->posts ) ),
		'result_count_current' => sprintf(
			'%d-%d',
			( $current_page - 1 ) * 8 + 1,
			min( $current_page * 8, $press_releases->found_posts )
		),
		'result_count_total'   => $press_releases->found_posts,
		'total_pages'          => $press_releases->max_num_pages,
		'current_page'         => $current_page,
		'pagination'           => $pagination,
		'first_page_link'      => 1 !== $current_page ? get_first_pagination_link() : '',
		'last_page_link'       => $current_page !== $press_releases->max_num_pages ? get_last_pagination_link( [ 'total' => $press_releases->max_num_pages ] ) : '',
	];

	// Return the rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}
