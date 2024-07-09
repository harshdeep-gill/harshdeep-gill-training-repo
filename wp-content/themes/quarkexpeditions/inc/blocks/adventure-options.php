<?php
/**
 * Block: Adventure Options.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\AdventureOptions;

use WP_Error;
use WP_Query;

use function Quark\AdventureOptions\get_cards_data;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\AdventureOptions\POST_TYPE as ADVENTURE_OPTIONS_POST_TYPE;

const BLOCK_NAME = 'quark/adventure-options';
const COMPONENT  = 'parts.adventure-options';

/**
 * Block initialization.
 *
 * @return void
 */
function bootstrap(): void {
	// Avoid registering in admin to fix a conflict with Blade views.
	if ( ! is_admin() ) {
		add_action( 'wp_loaded', __NAMESPACE__ . '\\register' );
	}
}

/**
 * Register block on the front-end.
 *
 * @return void
 */
function register(): void {
	// Register block.
	register_block_type(
		BLOCK_NAME,
		[
			'attributes'      => [
				'termIDs' => [
					'type'    => 'array',
					'default' => [],
				],
			],
			'render_callback' => __NAMESPACE__ . '\\render',
		]
	);
}

/**
 * Render this block.
 *
 * @param mixed[] $attributes Block attributes.
 *
 * @return string
 */
function render( array $attributes = [] ): string {
	// Current post ID.
	$current_post_id = get_the_ID();

	// Check if post id available.
	if ( empty( $current_post_id ) ) {
		return '';
	}

	// Get the terms.
	$terms = get_the_terms( $current_post_id, ADVENTURE_OPTION_CATEGORY );

	// Initialize tax query.
	$tax_query = [
		'taxonomy'         => ADVENTURE_OPTION_CATEGORY,
		'field'            => 'term_id',
		'include_children' => false,
		'operator'         => 'IN',
	];

	// Check if terms were selected in the editor.
	if ( ! empty( $attributes['termIDs'] ) && is_array( $attributes['termIDs'] ) ) {
		$tax_query['terms'] = $attributes['termIDs'];
	} elseif ( ! empty( $terms ) && ! $terms instanceof WP_Error ) {
		$tax_query['terms'] = array_map(
			function ( $term ) {
				return absint( $term->term_id );
			},
			$terms
		);
	} else {
		// Bail.
		return '';
	}

	// Build query args.
	$args = [
		'post_type'              => ADVENTURE_OPTIONS_POST_TYPE,
		'post_status'            => 'publish',
		'fields'                 => 'ids',
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'tax_query'              => [ $tax_query ],
	];

	// Get posts.
	$posts = new WP_Query( $args );

	// Get posts in array format of IDs.
	$post_ids = $posts->posts;

	// Check if we have posts.
	if ( empty( $post_ids ) ) {
		return '';
	}

	// Get blog post cards data.
	$cards_data = get_cards_data( array_map( 'absint', array_diff( $post_ids, [ $current_post_id ] ) ) );

	// Return built component.
	return quark_get_component(
		COMPONENT,
		[
			'cards' => $cards_data,
		]
	);
}
