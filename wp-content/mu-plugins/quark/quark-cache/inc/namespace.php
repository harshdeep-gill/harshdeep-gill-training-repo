<?php
/**
 * Namespace functions.
 *
 * @package quark-cache
 */

namespace Quark\Cache;

use WP_CLI;
use WP_Post;

use function Quark\AdventureOptions\bust_post_cache as bust_adventure_option_post_cache;
use function Quark\Blog\bust_post_cache as bust_blog_post_cache;
use function Quark\Departures\bust_post_cache as bust_departure_post_cache;
use function Quark\Expeditions\bust_post_cache as bust_expedition_post_cache;
use function Quark\Itineraries\bust_post_cache as bust_itinerary_post_cache;
use function Quark\StaffMembers\bust_post_cache as bust_staff_member_post_cache;
use function Quark\Brochures\bust_post_cache as bust_brochure_cache;
use function Quark\CabinCategories\bust_post_cache as bust_cabin_category_cache;
use function Quark\Regions\bust_post_cache as bust_region_cache;
use function Quark\Ports\bust_post_cache as bust_port_cache;
use function Quark\Ships\bust_post_cache as bust_ship_cache;
use function Quark\ShipDecks\bust_post_cache as bust_ship_deck_cache;
use function Quark\PolicyPages\bust_post_cache as bust_policy_page_cache;
use function Quark\Blog\Authors\bust_post_cache as bust_author_cache;
use function Quark\Offers\bust_post_cache as bust_offer_cache;
use function Quark\ExclusionSets\bust_post_cache as bust_exclusion_set_cache;
use function Quark\ItineraryDays\bust_post_cache as bust_itinerary_day_cache;
use function Quark\InclusionSets\bust_post_cache as bust_inclusion_set_cache;
use function Quark\Expeditions\PrePostTripOptions\bust_post_cache as bust_pre_post_trip_option_cache;

use const Quark\AdventureOptions\POST_TYPE as ADVENTURE_OPTION_POST_TYPE;
use const Quark\Blog\Authors\POST_TYPE as AUTHOR_POST_TYPE;
use const Quark\Blog\POST_TYPE as BLOG_POST_TYPE;
use const Quark\Brochures\POST_TYPE as BROCHURE_POST_TYPE;
use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\Cache\Edge\CACHED_POST_TYPE_SLUGS;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\ExclusionSets\POST_TYPE as EXCLUSION_SET_POST_TYPE;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Expeditions\PrePostTripOptions\POST_TYPE as PRE_POST_TRIP_OPTION_POST_TYPE;
use const Quark\InclusionSets\POST_TYPE as INCLUSION_SET_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\ItineraryDays\POST_TYPE as ITINERARY_DAY_POST_TYPE;
use const Quark\Offers\POST_TYPE as OFFER_POST_TYPE;
use const Quark\PolicyPages\POST_TYPE as POLICY_PAGE_POST_TYPE;
use const Quark\Ports\POST_TYPE as PORTS_POST_TYPE;
use const Quark\Regions\POST_TYPE as REGION_POST_TYPE;
use const Quark\ShipDecks\POST_TYPE as SHIP_DECK_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;
use const Quark\StaffMembers\POST_TYPE as STAFF_MEMBER_POST_TYPE;

// Pricing blocks.
const PRICING_BLOCKS = [
	'quark/book-departures-expeditions',
	'quark/book-departures-ships',
	'quark/dates-and-rates',
	'quark/detailed-expeditions-carousel',
	'quark/expedition-details',
	'quark/expedition-search',
	'quark/expeditions',
	'quark/itineraries',
	'quark/product-cards',
	'quark/product-departures-card',
];

/**
 * Bootstrap.
 *
 * @return void
 */
function bootstrap(): void {
	// CLI commands.
	if ( defined( 'WP_CLI' ) && true === WP_CLI ) {
		WP_CLI::add_command( 'quark-cache pricing-information', __NAMESPACE__ . '\\WP_CLI\\Pricing_Information' );
	}

	// Set meta for pricing block page.
	add_action( 'save_post', __NAMESPACE__ . '\\set_meta_for_pricing_block_posts' );

	// Bust cache for term update.
	add_action( 'edited_term', __NAMESPACE__ . '\\bust_cache_on_term_update', 10, 3 );
}

/**
 * Set post meta for any post that contains a block which displays pricing.
 * We require this to selectively bust only those posts' edge cache.
 *
 * @param int $post_id Post ID.
 *
 * @return bool True if meta set, false otherwise.
 */
function set_meta_for_pricing_block_posts( int $post_id = 0 ): bool {
	// Bail if saving a revision OR Bail if doing autosave.
	if ( wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
		return false;
	}

	// Get post ID.
	if ( 0 === $post_id ) {
		$post_id = absint( get_the_ID() );
	}

	// Get post.
	$post = get_post( $post_id );

	// Bail if no post.
	if ( ! $post instanceof WP_Post || ! in_array( $post->post_type, CACHED_POST_TYPE_SLUGS, true ) ) {
		return false;
	}

	// get post content.
	$content = get_post_field( 'post_content', $post_id );

	// Parse blocks.
	$blocks = parse_blocks( $content );

	// Skip if we don't have any blocks.
	if ( empty( $blocks ) ) {
		return false;
	}

	// Flatten blocks.
	$blocks            = _flatten_blocks( $blocks );
	$has_pricing_block = false;

	// Loop through blocks to find the pricing blocks.
	foreach ( $blocks as $block ) {
		// Skip if we don't have a block name.
		if ( empty( $block['blockName'] ) ) {
			continue;
		}

		// Skip if block name is not in the pricing blocks.
		if ( in_array( $block['blockName'], PRICING_BLOCKS, true ) ) {
			// Set flag to true.
			$has_pricing_block = true;

			// break the loop.
			break;
		}
	}

	// Update post meta if pricing block found.
	if ( true === $has_pricing_block ) {
		// Set pricing block meta.
		update_post_meta( $post_id, '_has_a_block_with_pricing_information', true );

		// Return true if pricing block found.
		return true;
	} else {
		delete_post_meta( $post_id, '_has_a_block_with_pricing_information' );
	}

	// Return false if no pricing block found.
	return false;
}

/**
 * Bust cache on term update.
 *
 * @param int    $term_id Term ID.
 * @param int    $tt_id Term taxonomy ID.
 * @param string $taxonomy Taxonomy slug.
 */
function bust_cache_on_term_update( int $term_id = 0, int $tt_id = 0, string $taxonomy = '' ): void {
	// Bail if no term ID or taxonomy.
	if ( empty( $term_id ) || empty( $taxonomy ) ) {
		return;
	}

	// Get object IDs.
	$object_ids = get_objects_in_term( $term_id, $taxonomy );

	// Bail if empty or not an array.
	if ( empty( $object_ids ) || ! is_array( $object_ids ) ) {
		return;
	}

	foreach ( $object_ids as $object_id ) {
		// Convert to int.
		$object_id = absint( $object_id );

		if ( empty( $object_id ) ) {
			continue;
		}

		// Bust post cache.
		bust_post_cache_by_id( $object_id );
	}
}

/**
 * Bust post cache by id.
 *
 * @param int $post_id Post ID.
 *
 * @return void
 */
function bust_post_cache_by_id( int $post_id = 0 ): void {
			// Get post type.
		$post_type = get_post_type( $post_id );

	switch ( $post_type ) {
		case DEPARTURE_POST_TYPE: {
			// Bust cache for departure.
			bust_departure_post_cache( $post_id );
			break;
		}
		case EXPEDITION_POST_TYPE: {
			// Bust cache for expedition.
			bust_expedition_post_cache( $post_id );
			break;
		}
		case ITINERARY_POST_TYPE: {
			// Bust cache for itinerary.
			bust_itinerary_post_cache( $post_id );
			break;
		}
		case STAFF_MEMBER_POST_TYPE: {
			// Bust cache for staff member.
			bust_staff_member_post_cache( $post_id );
			break;
		}
		case ADVENTURE_OPTION_POST_TYPE: {
			// Bust cache for adventure option.
			bust_adventure_option_post_cache( $post_id );
			break;
		}
		case BLOG_POST_TYPE: {
			// Bust cache for blog post.
			bust_blog_post_cache( $post_id );
			break;
		}
		case CABIN_CATEGORY_POST_TYPE: {
			// Bust cache for cabin category.
			bust_cabin_category_cache( $post_id );
			break;
		}
		case SHIP_POST_TYPE: {
			// Bust cache for ship.
			bust_ship_cache( $post_id );
			break;
		}
		case SHIP_DECK_POST_TYPE: {
			// Bust cache for ship deck.
			bust_ship_deck_cache( $post_id );
			break;
		}
		case REGION_POST_TYPE: {
			// Bust cache for region.
			bust_region_cache( $post_id );
			break;
		}
		case PORTS_POST_TYPE: {
			// Bust cache for port.
			bust_port_cache( $post_id );
			break;
		}
		case BROCHURE_POST_TYPE: {
			// Bust cache for brochure.
			bust_brochure_cache( $post_id );
			break;
		}
		case POLICY_PAGE_POST_TYPE: {
			// Bust cache for policy page.
			bust_policy_page_cache( $post_id );
			break;
		}
		case AUTHOR_POST_TYPE: {
			// Bust cache for author.
			bust_author_cache( $post_id );
			break;
		}
		case OFFER_POST_TYPE: {
			// Bust cache for offer.
			bust_offer_cache( $post_id );
			break;
		}
		case EXCLUSION_SET_POST_TYPE: {
			// Bust cache for exclusion set.
			bust_exclusion_set_cache( $post_id );
			break;
		}
		case ITINERARY_DAY_POST_TYPE: {
			// Bust cache for itinerary day.
			bust_itinerary_day_cache( $post_id );
			break;
		}
		case INCLUSION_SET_POST_TYPE: {
			// Bust cache for inclusion set.
			bust_inclusion_set_cache( $post_id );
			break;
		}
		case PRE_POST_TRIP_OPTION_POST_TYPE: {
			// Bust cache for pre/post trip option.
			bust_pre_post_trip_option_cache( $post_id );
			break;
		}
	}
}
