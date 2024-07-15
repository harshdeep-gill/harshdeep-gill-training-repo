<?php
/**
 * Migrate: Expedition.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use cli\progress\Bar;
use WP_CLI;
use WP_Post;
use WP_Term;
use WP_Error;
use WP_CLI\ExitException;
use Quark\Migration\Drupal\Block_Converter;

use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\get_post_by_id;
use function Quark\Migration\Drupal\get_term_by_id;
use function Quark\Migration\Drupal\prepare_content;
use function Quark\Migration\Drupal\prepare_seo_data;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\Expeditions\POST_TYPE;
use const Quark\Expeditions\EXPEDITION_CATEGORY_TAXONOMY;
use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\AdventureOptions\POST_TYPE as ADVENTURE_OPTION_POST_TYPE;
use const Quark\Expeditions\PrePostTripOptions\POST_TYPE as PRE_POST_TRIP_POST_TYPE;

/**
 * Class Expedition.
 */
class Expedition {

	/**
	 * Migrate all Expedition.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts(): void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch Expedition data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for "Expedition" post-type!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "Expedition" post-type', count( $data ) );

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
			WP_CLI::warning( 'Unable to insert/update post!' );
		} elseif ( $normalized_post['meta_input']['related_itineraries'] ) {
			// set related itineraries.
			update_field( 'related_itineraries', $normalized_post['meta_input']['related_itineraries'], $output );
		}
	}

	/**
	 * Normalize drupal post data.
	 *
	 * @param array{}|array<string, int|string> $item Drupal post data.
	 *
	 * @return array{}|array{
	 *     post_type : string,
	 *     post_author : string,
	 *     post_title : string,
	 *     post_date : string,
	 *     post_date_gmt : string,
	 *     post_modified : string,
	 *     post_modified_gmt : string,
	 *     post_content : string,
	 *     post_status : string,
	 *     comment_status : string,
	 *     ping_status : string,
	 *     meta_input : array{
	 *         drupal_id : int,
	 *         related_itineraries : array<int>|array{},
	 *     },
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
		$post_name    = '';
		$post_content = '';

		// Title.
		if ( is_string( $item['title'] ) && ! empty( $item['title'] ) ) {
			$title = strval( qrk_sanitize_attribute( $item['title'] ) );
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

		// Post name.
		if ( ! empty( $item['drupal_url'] ) && is_string( $item['drupal_url'] ) ) {
			/**
			 * Break the url into parts and use the last part as post name.
			 * i.e. - /expeditions/sea-spirit.
			 */
			$parts     = explode( '/', $item['drupal_url'] );
			$post_name = end( $parts );
		}

		// Prepare post data.
		$data = [
			'post_type'         => POST_TYPE,
			'post_author'       => '1',
			'post_title'        => $title,
			'post_name'         => $post_name,
			'post_date'         => $created_at,
			'post_date_gmt'     => $created_at,
			'post_modified'     => $modified_at,
			'post_modified_gmt' => $modified_at,
			'post_content'      => prepare_content( $post_content ),
			'post_excerpt'      => trim( wp_strip_all_tags( strval( $item['post_excerpt'] ) ) ),
			'post_status'       => $status,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
			'meta_input'        => [],
		];

		// Set expedition category term.
		if ( ! empty( $item['expedition_category_id'] ) ) {
			$term = get_term_by_id( absint( $item['expedition_category_id'] ), EXPEDITION_CATEGORY_TAXONOMY );

			// Check if term exist.
			if ( $term instanceof WP_Term ) {
				$data['tax_input'][ EXPEDITION_CATEGORY_TAXONOMY ][] = $term->term_id;
			}
		}

		// Set destination_ids term.
		if ( ! empty( $item['destination_ids'] ) && is_string( $item['destination_ids'] ) ) {
			$destination_ids = explode( ',', $item['destination_ids'] );

			// Push primary destination id to $destination_ids.
			if ( ! empty( $item['primary_destination_id'] ) ) {
				$destination_ids[] = $item['primary_destination_id'];
			}

			// Loop through destination ids.
			foreach ( $destination_ids as $destination_id ) {
				$term = get_term_by_id( absint( $destination_id ), DESTINATION_TAXONOMY );

				// Check if term exist.
				if ( $term instanceof WP_Term ) {
					$data['tax_input'][ DESTINATION_TAXONOMY ][] = $term->term_id;
				}
			}
		}

		// SEO meta data.
		if ( ! empty( $item['metatags'] ) && is_string( $item['metatags'] ) ) {
			$seo_data = prepare_seo_data( json_decode( $item['metatags'], true ) );

			// Merge seo data if not empty.
			if ( ! empty( $seo_data ) ) {
				$data['meta_input'] = array_merge( $seo_data, $data['meta_input'] );
			}
		}

		// Set adv_options_included.
		if ( ! empty( $item['adv_options_included'] ) && is_string( $item['adv_options_included'] ) ) {
			$adv_options_included_ids = explode( ',', $item['adv_options_included'] );

			// Init adv_options_included.
			$adv_options_included = [];

			// Loop through adv_options_included_ids.
			foreach ( $adv_options_included_ids as $adv_option_included_id ) {
				$adv_option_included_id = absint( $adv_option_included_id );

				// Get post by drupal id.
				$adv_option_included = get_post_by_id( $adv_option_included_id, ADVENTURE_OPTION_POST_TYPE );

				// Check if post exist.
				if ( $adv_option_included instanceof WP_Post ) {
					$adv_options_included[] = $adv_option_included->ID;
				}
			}

			// Set related included_activities.
			$data['meta_input']['included_activities'] = $adv_options_included;
		}

		// Set related_adventure_options.
		if ( ! empty( $item['adv_options_extra'] ) && is_string( $item['adv_options_extra'] ) ) {
			$adv_options_extra_ids = explode( ',', $item['adv_options_extra'] );

			// Init adv_options_extra.
			$adv_options_extra = [];

			// Loop through adv_options_included_ids.
			foreach ( $adv_options_extra_ids as $adv_options_extra_id ) {
				$adv_options_extra_id = absint( $adv_options_extra_id );

				// Get post by drupal id.
				$adv_option_extra = get_post_by_id( $adv_options_extra_id, ADVENTURE_OPTION_POST_TYPE );

				// Check if post exist.
				if ( $adv_option_extra instanceof WP_Post ) {
					$adv_options_extra[] = $adv_option_extra->ID;
				}
			}

			// Set related adventure_options.
			$data['meta_input']['related_adventure_options'] = $adv_options_extra;
		}

		// Set pre_post_options.
		if ( ! empty( $item['pre_post_options_paragraph_id'] ) ) {
			$pre_post_options_ids = $this->get_pre_post_trip_options( absint( $item['pre_post_options_paragraph_id'] ) );

			// Check if we have pre_post_options_ids.
			if ( ! empty( $pre_post_options_ids ) ) {
				// Set related pre_post_options.
				$data['meta_input']['related_pre_post_trips'] = $pre_post_options_ids;
			}
		}

		// Init itineraries.
		$itineraries = [];

		// Set itineraries.
		if ( ! empty( $item['itineraries'] ) && is_string( $item['itineraries'] ) ) {
			$itineraries_ids = explode( ',', $item['itineraries'] );

			// Loop through itineraries ids.
			foreach ( $itineraries_ids as $itinerary_id ) {
				$itinerary_id = absint( $itinerary_id );

				// Get post by drupal id.
				$itinerary = get_post_by_id( $itinerary_id, ITINERARY_POST_TYPE );

				// Check if post exist.
				if ( $itinerary instanceof WP_Post ) {
					$itineraries[] = absint( $itinerary->ID );
				}
			}
		}

		// Set related itineraries.
		$data['meta_input']['related_itineraries'] = $itineraries;

		// Set drupal id metadata.
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
			( SELECT count(1) FROM redirect WHERE redirect_source__path = CONCAT( 'node/', node.nid ) ) AS is_redirected,
			( SELECT alias AS drupal_url FROM path_alias WHERE path = CONCAT( '/node/', node.nid ) ORDER BY id DESC LIMIT 0, 1 ) AS drupal_url,
			body.body_value AS post_content,
			body.body_summary AS post_excerpt,
			field_expedition_category.field_expedition_category_target_id AS expedition_category_id,
			field_metatags.field_metatags_value AS metatags,
			field_primary_destination.field_primary_destination_target_id AS primary_destination_id,
			field_pre_post_options.field_pre_post_options_target_id AS pre_post_options_paragraph_id,
			(SELECT GROUP_CONCAT( field_destinations_target_id ORDER BY delta SEPARATOR ', ' ) FROM node__field_destinations AS field_destinations WHERE node.nid = field_destinations.entity_id AND field_destinations.langcode = node.langcode) AS destination_ids,
			(SELECT GROUP_CONCAT( field_itineraries_target_id ORDER BY delta SEPARATOR ', ' ) FROM node__field_itineraries AS field_itineraries WHERE node.nid = field_itineraries.entity_id AND field_itineraries.langcode = node.langcode) AS itineraries,
			(SELECT GROUP_CONCAT( field_adv_options_included_target_id ORDER BY delta SEPARATOR ', ' ) FROM node__field_adv_options_included AS field_adv_options_included WHERE node.nid = field_adv_options_included.entity_id AND field_adv_options_included.langcode = node.langcode) AS adv_options_included,
			(SELECT GROUP_CONCAT( field_adv_options_extra_target_id ORDER BY delta SEPARATOR ', ' ) FROM node__field_adv_options_extra AS field_adv_options_extra WHERE node.nid = field_adv_options_extra.entity_id AND field_adv_options_extra.langcode = node.langcode) AS adv_options_extra
		FROM
			node
				LEFT JOIN node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
				LEFT JOIN node__body AS body ON node.nid = body.entity_id AND node.langcode = body.langcode
				LEFT JOIN node__field_expedition_category AS field_expedition_category ON node.nid = field_expedition_category.entity_id AND node.langcode = field_expedition_category.langcode
				LEFT JOIN node__field_pre_post_options AS field_pre_post_options ON node.nid = field_pre_post_options.entity_id AND node.langcode = field_pre_post_options.langcode
				LEFT JOIN node__field_metatags AS field_metatags ON node.nid = field_metatags.entity_id AND node.langcode = field_metatags.langcode
				LEFT JOIN node__field_primary_destination AS field_primary_destination ON node.nid = field_primary_destination.entity_id AND node.langcode = field_primary_destination.langcode
		WHERE
			node.type = 'expedition'";

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
	 * Get pre_post_trip_options posts ID from paragraph.
	 *
	 * @param int $pre_post_options_paragraph_id Pre Post Trip Options Paragraph ID.
	 *
	 * @return array{}|array<int> Pre Post Trip Options.
	 */
	private function get_pre_post_trip_options( int $pre_post_options_paragraph_id = 0 ): array {
		// if no paragraph id return empty array.
		if ( empty( $pre_post_options_paragraph_id ) ) {
			return [];
		}

		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_component_background.field_component_background_value as background,
			field_options_lead_in.field_options_lead_in_value as description,
			( SELECT GROUP_CONCAT( field_options_items_target_id ORDER BY delta SEPARATOR ', ' ) FROM paragraph__field_options_items AS field_options_items WHERE paragraph.id = field_options_items.entity_id AND field_options_items.langcode = paragraph.langcode ) AS options_items
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_component_background AS field_component_background ON paragraph.id = field_component_background.entity_id AND paragraph.langcode = field_component_background.langcode
				LEFT JOIN paragraph__field_options_lead_in AS field_options_lead_in ON paragraph.id = field_options_lead_in.entity_id AND paragraph.langcode = field_options_lead_in.langcode
		WHERE
			paragraph.type = 'pre_post_trip_option' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Get database connection.
		$drupal_database = get_database();

		// Fetch data.
		$result = $drupal_database->get_row( $drupal_database->prepare( $query, $pre_post_options_paragraph_id ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch pre_post_trip_option paragraph data!' );

			// Bail out.
			return [];
		}

		// init posts.
		$posts = [];

		// Check if options_items are available.
		if ( ! empty( $result['options_items'] ) ) {
			$options_items = explode( ',', $result['options_items'] );

			// Loop through each block.
			foreach ( $options_items as $options_item ) {
				$post = get_post_by_id( absint( $options_item ), PRE_POST_TRIP_POST_TYPE );

				// Check if post found.
				if ( $post instanceof WP_Post ) {
					$posts[] = $post->ID;
				}
			}
		}

		// Return posts.
		return $posts;
	}
}
