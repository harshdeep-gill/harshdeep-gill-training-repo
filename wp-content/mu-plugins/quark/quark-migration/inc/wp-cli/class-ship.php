<?php
/**
 * Migrate: Ship nodes from Drupal to WordPress CPT.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use WP_Post;
use WP_CLI;
use WP_Error;
use WP_Term;
use cli\progress\Bar;
use WP_CLI\ExitException;

use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\get_term_by_id;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\get_post_by_id;
use function Quark\Migration\Drupal\download_file_by_mid;
use function Quark\Migration\Drupal\prepare_seo_data;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\Ships\POST_TYPE;
use const Quark\ShipDecks\POST_TYPE as SHIP_DECK_POST_TYPE;
use const Quark\Ships\SHIP_CATEGORY_TAXONOMY;

/**
 * Class Ship.
 */
class Ship {

	/**
	 * Migrate all Ships.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts(): void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch ships data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for ship!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "Ship" post-type', count( $data ) );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Start inserting posts.
		foreach ( $data as $item ) {
			// Insert post.
			$progress->tick();
			$this->insert_post( $item );
		}

		// Finish progress bar.
		$progress->finish();

		// Halt for a sec.
		sleep( 1 );

		// Recount terms.
		WP_CLI::log( 'Recounting terms...' );
		WP_CLI::runcommand( 'term recount ' . SHIP_CATEGORY_TAXONOMY );
	}

	/**
	 * Insert post by passing drupal data.
	 *
	 * @param array{}|array<string, int|string> $drupal_post Drupal post data.
	 *
	 * @return void
	 */
	public function insert_post( array $drupal_post = [] ): void {
		// Normalize drupal post data.
		$normalized_post = $this->normalize_drupal_post( $drupal_post );

		// Make sure data is normalized.
		if ( empty( $normalized_post ) ) {
			WP_CLI::warning( 'Unable to normalize drupal post data!' );

			// Bail out.
			return;
		}

		// Check post exist or not.
		$wp_post = get_post_by_id( $normalized_post['meta_input']['drupal_id'], POST_TYPE );

		// Insert/update post.
		if ( ! empty( $wp_post ) ) {
			$normalized_post['ID'] = $wp_post->ID;
			$output                = wp_update_post( $normalized_post );
		} else {
			$output = wp_insert_post( $normalized_post );
		}

		// Check if post inserted/updated or not.
		if ( $output instanceof WP_Error ) {
			// Print error.
			WP_CLI::warning( 'Unable to insert/update ship - ' . $normalized_post['meta_input']['drupal_id'] );
		}
	}

	/**
	 * Normalize drupal post data.
	 *
	 * @param array{}|array<string, int|string> $item Drupal post data.
	 *
	 * @return array{}|array{
	 *     post_type: string,
	 *     post_author: string,
	 *     post_title: string,
	 *     post_date: string,
	 *     post_date_gmt: string,
	 *     post_modified: string,
	 *     post_modified_gmt: string,
	 *     post_name: string,
	 *     post_content: string,
	 *     post_excerpt: string,
	 *     post_status: string,
	 *     comment_status: string,
	 *     ping_status: string,
	 *     meta_input: array{
	 *          drupal_id : int,
	 *     }
	 * }
	 */
	public function normalize_drupal_post( array $item = [] ): array {
		// Bail out if empty.
		if ( empty( $item ) ) {
			return [];
		}

		// Normalize data.
		$nid          = ! empty( $item['nid'] ) ? absint( $item['nid'] ) : 0;
		$title        = '';
		$created_at   = gmdate( 'Y-m-d H:i:s' );
		$modified_at  = gmdate( 'Y-m-d H:i:s' );
		$status       = 'draft';
		$post_excerpt = '';
		$post_name    = '';

		// Title.
		if ( is_string( $item['title'] ) && ! empty( $item['title'] ) ) {
			$title = trim( $item['title'] );
		}

		// Created date.
		if ( ! empty( $item['created'] ) ) {
			$created_at = gmdate( 'Y-m-d H:i:s', absint( $item['created'] ) );
		}

		// Modified date.
		if ( ! empty( $item['changed'] ) ) {
			$modified_at = gmdate( 'Y-m-d H:i:s', absint( $item['changed'] ) );
		}

		// Status.
		if ( ! empty( $item['status'] ) && 1 === absint( $item['status'] ) ) {
			$status = 'publish';
		}

		// post content.
		if ( ! empty( $item['post_content'] ) ) {
			$post_content = strval( $item['post_content'] );
		}

		// post excerpt.
		if ( ! empty( $item['post_excerpt'] ) && is_string( $item['post_excerpt'] ) ) {
			$post_excerpt = wp_strip_all_tags( trim( $item['post_excerpt'] ) );
		}

		// Post name.
		if ( ! empty( $item['drupal_url'] ) && is_string( $item['drupal_url'] ) ) {
			/**
			 * Break the url into parts and use the last part as post name.
			 * i.e. - /expedition-ships/sea-spirit.
			 */
			$parts     = explode( '/', $item['drupal_url'] );
			$post_name = end( $parts );
		}

		// Default post content for ship.
		$post_content = '<!-- wp:quark/hero {"immersive":"bottom","contentOverlap":false, "syncPostThumbnail":true} -->
			<!-- wp:quark/breadcrumbs /-->

			<!-- wp:quark/hero-content -->
			<!-- wp:quark/hero-content-left -->
			<!-- wp:quark/hero-title {"title":"Expedition Ships","syncPostTitle":true } /-->
			<!-- /wp:quark/hero-content-left -->
			<!-- /wp:quark/hero-content -->
			<!-- /wp:quark/hero -->

			<!-- wp:quark/secondary-navigation -->
			<!-- wp:quark/secondary-navigation-menu -->
			<!-- wp:quark/secondary-navigation-item {"title":"Overview","url":{"url":"overview","text":"","newWindow":false}} /-->

			<!-- wp:quark/secondary-navigation-item {"title":"Features & Amenities","url":{"url":"features-and-amenities","text":"","newWindow":false}} /-->

			<!-- wp:quark/secondary-navigation-item {"title":"Deck Plans & Cabins","url":{"url":"deck-plans-and-cabins","text":"","newWindow":false}} /-->
			<!-- /wp:quark/secondary-navigation-menu -->

			<!-- wp:quark/secondary-navigation-cta-buttons -->
			<!-- wp:quark/button {"backgroundColor":"black","btnText":"Download Brochure"} /-->

			<!-- wp:quark/button {"url":{"url":"#upcoming-departures","text":"","newWindow":false},"btnText":"Upcoming Departures"} /-->
			<!-- /wp:quark/secondary-navigation-cta-buttons -->
			<!-- /wp:quark/secondary-navigation -->

			<!-- wp:quark/section {"anchor":"overview","title":"Overview","titleAlignment":"left","headingLevel":2} -->
			<!-- wp:paragraph -->
			<p></p>
			<!-- /wp:paragraph -->

			<!-- wp:quark/ship-specifications /-->

			<!-- wp:quark/collage -->
			<!-- wp:quark/collage-media-item /-->

			<!-- wp:quark/collage-media-item /-->

			<!-- wp:quark/collage-media-item /-->

			<!-- wp:quark/collage-media-item /-->
			<!-- /wp:quark/collage -->
			<!-- /wp:quark/section -->

			<!-- wp:quark/section {"title":"Features of the Vessel","titleAlignment":"left"} -->
			<!-- wp:quark/ship-vessel-features -->
			<!-- wp:quark/ship-vessel-features-card /-->

			<!-- wp:quark/ship-vessel-features-card /-->

			<!-- wp:quark/ship-vessel-features-card /-->
			<!-- /wp:quark/ship-vessel-features -->
			<!-- /wp:quark/section -->

			<!-- wp:quark/section {"anchor":"features-and-amenities","title":"Features & Amenities","titleAlignment":"left","headingLevel":2} -->
			<!-- wp:quark/ship-features-amenities -->
			<!-- wp:quark/ship-features-amenities-card /-->

			<!-- wp:quark/ship-features-amenities-card /-->

			<!-- wp:quark/ship-features-amenities-card /-->
			<!-- /wp:quark/ship-features-amenities -->
			<!-- /wp:quark/section -->

			<!-- wp:quark/section {"titleAlignment":"left","hasDescription":true} -->
			<!-- wp:quark/media-carousel /-->
			<!-- /wp:quark/section -->

			<!-- wp:quark/section {"titleAlignment":"left"} -->
			<!-- wp:quark/media-description-cards -->
			<!-- wp:quark/media-description-card /-->

			<!-- wp:quark/media-description-card /-->

			<!-- wp:quark/media-description-card /-->
			<!-- /wp:quark/media-description-cards -->
			<!-- /wp:quark/section -->

			<!-- wp:quark/section {"titleAlignment":"left"} -->
			<!-- wp:quark/ship-related-adventure-options /-->
			<!-- /wp:quark/section -->

			<!-- wp:quark/section {"anchor":"deck-plans-and-cabins","title":"Deck Plans & Cabins","titleAlignment":"left","headingLevel":2} -->
			<!-- wp:quark/ship-decks /-->
			<!-- /wp:quark/section -->

			<!-- wp:quark/section {"title":"Comparison of All Cabins","titleAlignment":"left"} -->
			<!-- wp:quark/ship-cabin-categories /-->
			<!-- /wp:quark/section -->

			<!-- wp:quark/section {"anchor":"upcoming-departures","title":"Upcoming Departures","titleAlignment":"left","headingLevel":2} -->
			<!-- wp:quark/book-departures-ships /-->
			<!-- /wp:quark/section -->';

		// Prepare post data.
		$data = [
			'post_type'         => POST_TYPE,
			'post_author'       => '1',
			'post_title'        => strval( qrk_sanitize_attribute( $title ) ),
			'post_date'         => $created_at,
			'post_date_gmt'     => $created_at,
			'post_modified'     => $modified_at,
			'post_modified_gmt' => $modified_at,
			'post_name'         => $post_name,
			'post_content'      => $post_content,
			'post_excerpt'      => $post_excerpt,
			'post_status'       => $status,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
			'meta_input'        => [],
		];

		// SEO meta data.
		if ( ! empty( $item['metatags'] ) && is_string( $item['metatags'] ) ) {
			$seo_data = prepare_seo_data( json_decode( $item['metatags'], true ) );

			// Merge seo data if not empty.
			if ( ! empty( $seo_data ) ) {
				$data['meta_input'] = array_merge( $seo_data, $data['meta_input'] );
			}
		}

		// Set fallback as excerpt if meta description is empty.
		if ( empty( $data['meta_input']['_yoast_wpseo_metadesc'] ) ) {
			$data['meta_input']['_yoast_wpseo_metadesc'] = $data['post_excerpt'];
		}

		// Get featured image.
		if ( ! empty( $item['hero_banner_id'] ) ) {
			$wp_thumbnail_id = $this->get_featured_image( absint( $item['hero_banner_id'] ) );

			// Set featured image.
			if ( ! empty( $wp_thumbnail_id ) ) {
				$data['meta_input']['_thumbnail_id'] = $wp_thumbnail_id;
			}
		}

		// Set ship_category term_id.
		if ( ! empty( $item['ship_category_term_id'] ) ) {
			$term = get_term_by_id( absint( $item['ship_category_term_id'] ), SHIP_CATEGORY_TAXONOMY );

			// Check if we have a valid term.
			if ( $term instanceof WP_Term ) {
				$data['tax_input'][ SHIP_CATEGORY_TAXONOMY ][] = $term->term_id;
			}
		}

		// Set Ship ID.
		if ( ! empty( $item['ship_id'] ) ) {
			$data['meta_input']['ship_code'] = strval( $item['ship_id'] );
		}

		// Set ship specifications - Breadth.
		if ( ! empty( $item['breadth'] ) ) {
			$data['meta_input']['breadth'] = strval( $item['breadth'] );
		}

		// Set ship specifications - Cruising Speed.
		if ( ! empty( $item['cruising_speed'] ) ) {
			$data['meta_input']['cruising_speed'] = strval( $item['cruising_speed'] );
		}

		// Set ship specifications - Gross Tonnage.
		if ( ! empty( $item['gross_tonnage'] ) ) {
			$data['meta_input']['gross_tonnage'] = strval( $item['gross_tonnage'] );
		}

		// Set ship specifications - Guest Ratio.
		if ( ! empty( $item['guest_ratio'] ) ) {
			$data['meta_input']['guest_ratio'] = strval( $item['guest_ratio'] );
		}

		// Set ship specifications - Guests.
		if ( ! empty( $item['guests'] ) ) {
			$data['meta_input']['guests'] = strval( $item['guests'] );
		}

		// Set ship specifications - Ice Class.
		if ( ! empty( $item['ice_class'] ) ) {
			$data['meta_input']['ice_class'] = strval( $item['ice_class'] );
		}

		// Set ship specifications - Year Refurbished.
		if ( ! empty( $item['year_refurbished'] ) ) {
			$data['meta_input']['year_refurbished'] = strval( $item['year_refurbished'] );
		}

		// Set ship specifications - Length.
		if ( ! empty( $item['length'] ) ) {
			$data['meta_input']['length'] = strval( $item['length'] );
		}

		// Set ship specifications - Lifeboats.
		if ( ! empty( $item['lifeboats'] ) ) {
			$data['meta_input']['lifeboats'] = strval( $item['lifeboats'] );
		}

		// Set ship specifications - Propulsion.
		if ( ! empty( $item['propulsion'] ) ) {
			$data['meta_input']['propulsion'] = strval( $item['propulsion'] );
		}

		// Set ship specifications - Registration.
		if ( ! empty( $item['registration'] ) ) {
			$data['meta_input']['registration'] = strval( $item['registration'] );
		}

		// Set ship specifications - Stabilizers.
		if ( ! empty( $item['stabilizers'] ) ) {
			$data['meta_input']['stabilizers'] = strval( $item['stabilizers'] );
		}

		// Set ship specifications - Staff and Crew.
		if ( ! empty( $item['staff_and_crew'] ) ) {
			$data['meta_input']['staff_and_crew'] = strval( $item['staff_and_crew'] );
		}

		// Set ship specifications - Voltage.
		if ( ! empty( $item['voltage'] ) ) {
			$data['meta_input']['voltage'] = strval( $item['voltage'] );
		}

		// Set ship specifications - Year Built.
		if ( ! empty( $item['year_built'] ) ) {
			$data['meta_input']['year_built'] = strval( $item['year_built'] );
		}

		// Set ship specifications - Zodiacs.
		if ( ! empty( $item['zodiacs'] ) ) {
			$data['meta_input']['zodiacs'] = strval( $item['zodiacs'] );
		}

		// Set deck_plan.
		if ( ! empty( $item['deck_plan'] ) ) {
			$deck_plan_image_id = download_file_by_mid( absint( $item['deck_plan'] ) );

			// Check if attachment exists.
			if ( ! $deck_plan_image_id instanceof WP_Error ) {
				$data['meta_input']['deck_plan_image'] = $deck_plan_image_id;
			}
		}

		// Set Ship Decks.
		if ( ! empty( $item['related_decks'] ) ) {
			$deck_ids      = array_map( 'absint', explode( ',', strval( $item['related_decks'] ) ) );
			$related_decks = [];

			// Loop through each deck.
			foreach ( $deck_ids as $deck_id ) {
				$deck_post = get_post_by_id( $deck_id, SHIP_DECK_POST_TYPE );

				// Is Valid post.
				if ( $deck_post instanceof WP_Post ) {
					$related_decks[] = $deck_post->ID;
				}
			}

			// Set related ship decks.
			$data['meta_input']['related_decks'] = $related_decks;
		}

		// Set Ship Amenities - Cabin.
		if ( ! empty( $item['amenities_cabin'] ) ) {
			$amenities_cabin = array_map( 'strval', explode( ',,', strval( $item['amenities_cabin'] ) ) );
			$cabin_count     = 0;

			// Set amenities cabin.
			foreach ( $amenities_cabin as $index => $amenity ) {
				$cabin_meta_key                        = sprintf( 'cabin_%d_item', $index );
				$data['meta_input'][ $cabin_meta_key ] = $amenity;

				// Set - cabin count.
				++$cabin_count;
			}

			// Set - set count.
			$data['meta_input']['cabin'] = $cabin_count;
		}

		// Set Ship Amenities - Aboard.
		if ( ! empty( $item['amenities_aboard'] ) ) {
			$amenities_aboard = array_map( 'strval', explode( ',,', strval( $item['amenities_aboard'] ) ) );
			$aboard_count     = 0;

			// Set amenities aboard.
			foreach ( $amenities_aboard as $index => $amenity ) {
				$aboard_meta_key                        = sprintf( 'aboard_%d_item', $index );
				$data['meta_input'][ $aboard_meta_key ] = $amenity;

				// Set - aboard count.
				++$aboard_count;
			}

			// Set - set count.
			$data['meta_input']['aboard'] = $aboard_count;
		}

		// Set Ship Amenities - Activities.
		if ( ! empty( $item['amenities_activities'] ) ) {
			$amenities_activities = array_map( 'strval', explode( ',,', strval( $item['amenities_activities'] ) ) );
			$activities_count     = 0;

			// Set amenities activities.
			foreach ( $amenities_activities as $index => $amenity ) {
				$activities_meta_key                        = sprintf( 'activities_%d_item', $index );
				$data['meta_input'][ $activities_meta_key ] = $amenity;

				// Set - activities count.
				++$activities_count;
			}

			// Set - set count.
			$data['meta_input']['activities'] = $activities_count;
		}

		// Set meta - drupal_id.
		$data['meta_input']['drupal_id'] = $nid;

		// Return normalized data.
		return $data;
	}

	/**
	 * Fetch data from drupal database.
	 *
	 * @return array{}|array<int, array<string, int|string>> Drupal data.
	 *
	 * @throws ExitException Exit on failure to fetch data.
	 */
	public function get_drupal_data(): array {
		// Get database connection.
		$drupal_database = get_database();

		// Query.
		$query = "SELECT
			node.nid,
			field_data.status,
			field_data.title,
			field_data.created,
			field_data.changed,
			field_data.publish_on,
			field_data.unpublish_on,
			( SELECT alias AS drupal_url FROM path_alias WHERE path = CONCAT( '/node/', node.nid ) ORDER BY id DESC LIMIT 0, 1 ) AS drupal_url,
			body.body_value AS post_content,
			body.body_summary AS post_excerpt,
			field_metatags.field_metatags_value AS metatags,
			field_ship_category.field_ship_category_target_id AS ship_category_term_id,
			field_ship_id.field_ship_id_value AS ship_id,
			field_breadth.field_breadth_value AS breadth,
			field_cruising_speed.field_cruising_speed_value AS cruising_speed,
			field_gross_tonnage.field_gross_tonnage_value AS gross_tonnage,
			field_guest_ratio.field_guest_ratio_value AS guest_ratio,
			field_guests.field_guests_value AS guests,
			field_ice_class.field_ice_class_value AS ice_class,
			field_year_refurbished.field_year_refurbished_value AS year_refurbished,
			field_length.field_length_value AS length,
			field_lifeboats.field_lifeboats_value AS lifeboats,
			field_propulsion.field_propulsion_value AS propulsion,
			field_registration.field_registration_value AS registration,
			field_stabilizers.field_stabilizers_value AS stabilizers,
			field_staff_and_crew.field_staff_and_crew_value AS staff_and_crew,
			field_voltage.field_voltage_value AS voltage,
			field_year_built.field_year_built_value AS year_built,
			field_zodiacs.field_zodiacs_value AS zodiacs,
			field_deck_plan.field_deck_plan_target_id AS deck_plan,
			field_hero_banner.field_hero_banner_target_id AS hero_banner_id,
			(SELECT GROUP_CONCAT( field_ship_decks_target_id ORDER BY delta SEPARATOR ', ' ) FROM node__field_ship_decks AS field_ship_decks WHERE node.nid = field_ship_decks.entity_id AND field_ship_decks.langcode = node.langcode) AS related_decks,
			(SELECT GROUP_CONCAT( field_amenities_cabin_value ORDER BY delta SEPARATOR ',, ' ) FROM node__field_amenities_cabin AS field_amenities_cabin WHERE node.nid = field_amenities_cabin.entity_id AND field_amenities_cabin.langcode = node.langcode) AS amenities_cabin,
			(SELECT GROUP_CONCAT( field_amenities_aboard_value ORDER BY delta SEPARATOR ',, ' ) FROM node__field_amenities_aboard AS field_amenities_aboard WHERE node.nid = field_amenities_aboard.entity_id AND field_amenities_aboard.langcode = node.langcode) AS amenities_aboard,
			(SELECT GROUP_CONCAT( field_amenities_activities_value ORDER BY delta SEPARATOR ',, ' ) FROM node__field_amenities_activities AS field_amenities_activities WHERE node.nid = field_amenities_activities.entity_id AND field_amenities_activities.langcode = node.langcode) AS amenities_activities
		FROM
			node
				LEFT JOIN node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
				LEFT JOIN `node__body` AS `body` ON node.nid = body.entity_id AND node.langcode = body.langcode
				LEFT JOIN `node__field_metatags` AS `field_metatags` ON node.nid = field_metatags.entity_id AND node.langcode = field_metatags.langcode
				LEFT JOIN `node__field_ship_category` AS `field_ship_category` ON node.nid = field_ship_category.entity_id AND node.langcode = field_ship_category.langcode
				LEFT JOIN `node__field_ship_id` AS `field_ship_id` ON node.nid = field_ship_id.entity_id AND node.langcode = field_ship_id.langcode
				LEFT JOIN `node__field_ship_specifications` AS `field_ship_specifications` ON node.nid = field_ship_specifications.entity_id AND node.langcode = field_ship_specifications.langcode
				LEFT JOIN paragraph__field_breadth AS field_breadth ON field_ship_specifications.field_ship_specifications_target_id = field_breadth.entity_id
				LEFT JOIN paragraph__field_cruising_speed AS field_cruising_speed ON field_ship_specifications.field_ship_specifications_target_id = field_cruising_speed.entity_id
				LEFT JOIN paragraph__field_draft AS field_draft ON field_ship_specifications.field_ship_specifications_target_id = field_draft.entity_id
				LEFT JOIN paragraph__field_gross_tonnage AS field_gross_tonnage ON field_ship_specifications.field_ship_specifications_target_id = field_gross_tonnage.entity_id
				LEFT JOIN paragraph__field_guest_ratio AS field_guest_ratio ON field_ship_specifications.field_ship_specifications_target_id = field_guest_ratio.entity_id
				LEFT JOIN paragraph__field_guests AS field_guests ON field_ship_specifications.field_ship_specifications_target_id = field_guests.entity_id
				LEFT JOIN paragraph__field_ice_class AS field_ice_class ON field_ship_specifications.field_ship_specifications_target_id = field_ice_class.entity_id
				LEFT JOIN paragraph__field_year_refurbished AS field_year_refurbished ON field_ship_specifications.field_ship_specifications_target_id = field_year_refurbished.entity_id
				LEFT JOIN paragraph__field_length AS field_length ON field_ship_specifications.field_ship_specifications_target_id = field_length.entity_id
				LEFT JOIN paragraph__field_lifeboats AS field_lifeboats ON field_ship_specifications.field_ship_specifications_target_id = field_lifeboats.entity_id
				LEFT JOIN paragraph__field_propulsion AS field_propulsion ON field_ship_specifications.field_ship_specifications_target_id = field_propulsion.entity_id
				LEFT JOIN paragraph__field_registration AS field_registration ON field_ship_specifications.field_ship_specifications_target_id = field_registration.entity_id
				LEFT JOIN paragraph__field_stabilizers AS field_stabilizers ON field_ship_specifications.field_ship_specifications_target_id = field_stabilizers.entity_id
				LEFT JOIN paragraph__field_staff_and_crew AS field_staff_and_crew ON field_ship_specifications.field_ship_specifications_target_id = field_staff_and_crew.entity_id
				LEFT JOIN paragraph__field_voltage AS field_voltage ON field_ship_specifications.field_ship_specifications_target_id = field_voltage.entity_id
				LEFT JOIN paragraph__field_year_built AS field_year_built ON field_ship_specifications.field_ship_specifications_target_id = field_year_built.entity_id
				LEFT JOIN paragraph__field_zodiacs AS field_zodiacs ON field_ship_specifications.field_ship_specifications_target_id = field_zodiacs.entity_id
				LEFT JOIN node__field_deck_plan AS field_deck_plan ON node.nid = field_deck_plan.entity_id AND node.langcode = field_deck_plan.langcode
				LEFT JOIN node__field_hero_banner AS field_hero_banner ON node.nid = field_hero_banner.entity_id AND node.langcode = field_hero_banner.langcode
		WHERE
			node.type = 'ship';";

		// Fetch data.
		$result = $drupal_database->get_results( $query, ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::error( 'Unable to fetch data!' );

			// Bail out.
			return [];
		}

		// Return data.
		return $result;
	}

	/**
	 * Get featured image.
	 *
	 * @param int $hero_id Hero block ID.
	 *
	 * @return int Hero block image.
	 */
	protected function get_featured_image( int $hero_id = 0 ): int {
		// Bail out if empty.
		if ( empty( $hero_id ) ) {
			return 0;
		}

		// Get database connection.
		$drupal_database = get_database();

		// Query.
		$query = "SELECT
			paragraph.id,
			field_hb_image.field_hb_image_target_id
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_hb_image AS field_hb_image ON paragraph.id = field_hb_image.entity_id AND paragraph.langcode = field_hb_image.langcode
		WHERE
			paragraph.type = 'hero_banner' AND paragraph.id = %s AND paragraph.langcode = 'en'";

		// Fetch data.
		$result = $drupal_database->get_row( $drupal_database->prepare( $query, $hero_id ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch hero_banner data!' );

			// Bail out.
			return 0;
		}

		// Set attributes.
		$image_target_id = ! empty( $result['field_hb_image_target_id'] ) ? download_file_by_mid( absint( $result['field_hb_image_target_id'] ) ) : '';

		// Check if image found.
		if ( $image_target_id instanceof WP_Error ) {
			return 0;
		}

		// Return hero block data.
		return absint( $image_target_id );
	}
}
