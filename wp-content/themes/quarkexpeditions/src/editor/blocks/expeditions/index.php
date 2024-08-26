<?php
/**
 * Block Name: Expeditions.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Expeditions;

use WP_Block;
use WP_Query;

use function Quark\Expeditions\get_details_data;

use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;

const COMPONENT = 'parts.expeditions';

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
 * @param mixed[]  $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Total posts.
	$total_departures = 0;
	$expedition_ids   = [];

	// If the selection is manual, we need to check if we have IDs.
	if ( 'manual' === $attributes['selection'] ) {
		// Return empty if manual select, but no IDs were selected.
		if ( empty( $attributes['ids'] ) ) {
			return '';
		}

		// Get the expedition IDs.
		$expedition_ids   = $attributes['ids'];
		$total_departures = count( $expedition_ids );
	} elseif ( 'byTerms' === $attributes['selection'] ) {
		// Return empty if selection by terms, but no terms or taxonomy were selected.
		if ( empty( $attributes['termIds'] ) || empty( $attributes['taxonomies'] ) ) {
			return '';
		}

		// Total posts.
		$total_departures = $attributes['totalPosts'];

		// Build query args.
		$args = [
			'post_type'              => EXPEDITION_POST_TYPE,
			'post_status'            => 'publish',
			'fields'                 => 'ids',
			'posts_per_page'         => $total_departures,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'orderby'                => 'date',
			'order'                  => 'DESC',
		];

		// Get the term IDs.
		$term_ids   = $attributes['termIds'];
		$taxonomies = $attributes['taxonomies'];

		// Build tax query.
		$tax_query = [
			'relation' => 'OR',
		];

		// Add taxonomies to query and add to the tax query.
		foreach ( $taxonomies as $taxonomy ) {
			$tax_query[] = [
				'taxonomy'         => $taxonomy,
				'terms'            => $term_ids,
				'field'            => 'term_id',
				'include_children' => false,
				'operator'         => 'IN',
			];
		}

		// Add tax query to args.
		$args['tax_query'] = $tax_query;

		// Get posts.
		$expedition_ids = new WP_Query( $args );
		$expedition_ids = $expedition_ids->posts;
	}

	// Initialize cards data.
	$cards = [];

	// Build cards.
	foreach ( $expedition_ids as $expedition_id ) {
		$card_data = get_details_data( $expedition_id );

		// Skip if no data.
		if ( empty( $card_data ) ) {
			continue;
		}

		// Build card data.
		$cards[] = [
			'title'            => $card_data['title'],
			'image_id'         => get_post_thumbnail_id( $expedition_id ),
			'itinerary_days'   => $card_data['duration'],
			'original_price'   => $card_data['from_price']['original'],
			'discounted_price' => $card_data['from_price']['discounted'],
		];
	}

	// Return built component.
	return quark_get_component(
		COMPONENT,
		[
			'cards' => $cards,
		]
	);
}
