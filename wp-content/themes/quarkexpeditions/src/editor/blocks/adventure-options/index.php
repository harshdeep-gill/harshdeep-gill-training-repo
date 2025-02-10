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
use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\AdventureOptions\POST_TYPE as ADVENTURE_OPTIONS_POST_TYPE;

const COMPONENT  = 'parts.adventure-options';
const BLOCK_NAME = 'quark/adventure-options';

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
	// Current post ID.
	$current_post_id = get_the_ID();

	// Check if post id available.
	if ( empty( $current_post_id ) ) {
		return '';
	}

	// Build query args.
	$args = [
		'post_type'              => ADVENTURE_OPTIONS_POST_TYPE,
		'post_status'            => 'publish',
		'fields'                 => 'ids',
		'posts_per_page'         => $attributes['total'],
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'orderby'                => 'date',
		'order'                  => 'DESC',
	];

	// Initialize tax query.
	$tax_query = [
		'field'            => 'term_id',
		'include_children' => false,
		'operator'         => 'IN',
	];

	// Check if terms were selected in the editor.
	if ( 'auto' === $attributes['selectionType'] ) {
		// Set destination taxonomy.
		$tax_query['taxonomy'] = DESTINATION_TAXONOMY;
		$terms                 = get_the_terms( $current_post_id, DESTINATION_TAXONOMY );

		// Check if terms are available.
		if ( empty( $terms ) || $terms instanceof WP_Error ) {
			return '';
		}

		// Set terms.
		$tax_query['terms'] = array_map(
			function ( $term ) {
				return absint( $term->term_id );
			},
			$terms
		);

		// Set the args.
		$args['tax_query']      = [ $tax_query ];
		$args['posts_per_page'] = $attributes['total'] + 1;
	} elseif ( ! empty( $attributes['termIDs'] ) && is_array( $attributes['termIDs'] ) && 'byCategory' === $attributes['selectionType'] ) {
		// Set adventure option category.
		$tax_query['taxonomy'] = ADVENTURE_OPTION_CATEGORY;
		$tax_query['terms']    = $attributes['termIDs'];

		// Set tax query.
		$args['tax_query'] = [ $tax_query ];
	} elseif ( ! empty( $attributes['ids'] ) && is_array( $attributes['ids'] ) && 'manual' === $attributes['selectionType'] ) {
		// Set post IDs.
		$args['post__in']       = $attributes['ids'];
		$args['orderby']        = 'post__in';
		$args['posts_per_page'] = count( $attributes['ids'] );
	} else {
		// Bail.
		return '';
	}

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
			'cards'             => $cards_data,
			'carousel_overflow' => $attributes['carouselOverflow'],
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
		'term_id' => [ 'termIDs' ],
		'post_id' => [ 'ids' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
