<?php
/**
 * Migrate: itinerary nodes from Drupal to WordPress CPT.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use cli\progress\Bar;
use WP_CLI;
use WP_Error;
use WP_CLI\ExitException;
use WP_Term;
use WP_Post;

use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\get_term_by_id;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\get_post_by_id;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\Itineraries\POST_TYPE;
use const Quark\Ports\POST_TYPE as PORT_POST_TYPE;
use const Quark\Brochures\POST_TYPE as BROCHURE_POST_TYPE;
use const Quark\InclusionSets\POST_TYPE as INCLUSION_SET_POST_TYPE;
use const Quark\ExclusionSets\POST_TYPE as EXCLUSION_SET_POST_TYPE;
use const Quark\ItineraryDays\POST_TYPE as ITINERARY_DAY_POST_TYPE;
use const Quark\PolicyPages\POST_TYPE as POLICY_PAGE_POST_TYPE;

/**
 * Class Itinerary.
 */
class Itinerary {

	/**
	 * Migrate all Itineraries.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts(): void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch Itineraries data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for Itinerary!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "Itinerary" post-type', count( $data ) );

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
			WP_CLI::warning( 'Unable to insert/update Itinerary - ' . $normalized_post['meta_input']['drupal_id'] );
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
		$post_content = '';
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
			'meta_input'        => [
				'drupal_id' => $nid,
			],
		];

		// Set Boilerplate.
		if ( ! empty( $item['boilerplate'] ) ) {
			$data['meta_input']['boilerplate'] = strval( $item['boilerplate'] );
		}

		// Set Softrip Package ID.
		if ( ! empty( $item['softrip_package_id'] ) ) {
			$data['meta_input']['softrip_package_id'] = strval( qrk_sanitize_attribute( $item['softrip_package_id'] ) );
		}

		// Set Duration in Days.
		if ( ! empty( $item['duration_in_days'] ) ) {
			$data['meta_input']['duration_in_days'] = absint( $item['duration_in_days'] );
		}

		// Set Start Location.
		if ( ! empty( $item['start_location'] ) ) {
			$start_location = get_term_by_id( absint( $item['start_location'] ) );

			// Check if term exist.
			if ( $start_location instanceof WP_Term ) {
				$data['meta_input']['start_location'] = $start_location->term_id;
			}
		}

		// Set End Location.
		if ( ! empty( $item['end_location'] ) ) {
			$end_location = get_term_by_id( absint( $item['end_location'] ) );

			// Check if term exist.
			if ( $end_location instanceof WP_Term ) {
				$data['meta_input']['end_location'] = $end_location->term_id;
			}
		}

		// Set Embarkation Port.
		if ( ! empty( $item['embarkation_port'] ) ) {
			$embarkation_port = get_post_by_id( absint( $item['embarkation_port'] ), PORT_POST_TYPE, 'drupal_tid' );

			// Check if post exist.
			if ( $embarkation_port instanceof WP_Post ) {
				$data['meta_input']['embarkation_port'] = $embarkation_port->ID;
			}
		}

		// Set Disembarkation Port.
		if ( ! empty( $item['disembarkation_port'] ) ) {
			$disembarkation_port = get_post_by_id( absint( $item['disembarkation_port'] ), PORT_POST_TYPE, 'drupal_tid' );

			// Check if post exist.
			if ( $disembarkation_port instanceof WP_Post ) {
				$data['meta_input']['disembarkation_port'] = $disembarkation_port->ID;
			}
		}

		// Set Brochure ID.
		if ( ! empty( $item['brochure_id'] ) ) {
			$brochure_id = get_post_by_id( absint( $item['brochure_id'] ), BROCHURE_POST_TYPE, 'drupal_mid' );

			// Check if post exist.
			if ( $brochure_id instanceof WP_Post ) {
				$data['meta_input']['brochure'] = $brochure_id->ID;
			}
		}

		// Set Map image ID.
		if ( ! empty( $item['map_id'] ) ) {
			$map_id = get_post_by_id( absint( $item['map_id'] ), 'attachment', 'drupal_mid' );

			// Check if post exist.
			if ( $map_id instanceof WP_Post ) {
				$data['meta_input']['map'] = $map_id->ID;
			}
		}

		// Set Mandatory Transfer Price - USD.
		if ( ! empty( $item['mandatory_transfer_price_usd'] ) ) {
			$data['meta_input']['mandatory_transfer_price_usd'] = number_format( floatval( $item['mandatory_transfer_price_usd'] ), 2 );
		}

		// Set Mandatory Transfer Price - AUD.
		if ( ! empty( $item['mandatory_transfer_price_aud'] ) ) {
			$data['meta_input']['mandatory_transfer_price_aud'] = number_format( floatval( $item['mandatory_transfer_price_aud'] ), 2 );
		}

		// Set Mandatory Transfer Price - CAD.
		if ( ! empty( $item['mandatory_transfer_price_cad'] ) ) {
			$data['meta_input']['mandatory_transfer_price_cad'] = number_format( floatval( $item['mandatory_transfer_price_cad'] ), 2 );
		}

		// Set Mandatory Transfer Price - EUR.
		if ( ! empty( $item['mandatory_transfer_price_eur'] ) ) {
			$data['meta_input']['mandatory_transfer_price_eur'] = number_format( floatval( $item['mandatory_transfer_price_eur'] ), 2 );
		}

		// Set Mandatory Transfer Price - GBP.
		if ( ! empty( $item['mandatory_transfer_price_gbp'] ) ) {
			$data['meta_input']['mandatory_transfer_price_gbp'] = number_format( floatval( $item['mandatory_transfer_price_gbp'] ), 2 );
		}

		// Set Mandatory Transfer Package Inclusions.
		if ( ! empty( $item['mtp_inclusion_id'] ) ) {
			$mtp_inclusion_id = get_post_by_id( absint( $item['mtp_inclusion_id'] ), INCLUSION_SET_POST_TYPE, 'drupal_id' );

			// Check if post exist.
			if ( $mtp_inclusion_id instanceof WP_Post ) {
				$data['meta_input']['mtp_inclusion_id'] = $mtp_inclusion_id->ID;
			}
		}

		// Set Mandatory Transfer Package Exclusions.
		if ( ! empty( $item['mtp_exclusion_id'] ) ) {
			$mtp_exclusion_id = get_post_by_id( absint( $item['mtp_exclusion_id'] ), EXCLUSION_SET_POST_TYPE, 'drupal_id' );

			// Check if post exist.
			if ( $mtp_exclusion_id instanceof WP_Post ) {
				$data['meta_input']['mtp_exclusion_id'] = $mtp_exclusion_id->ID;
			}
		}

		// Set Supplement Price - USD.
		if ( ! empty( $item['supplement_price_usd'] ) ) {
			$data['meta_input']['supplement_price_usd'] = number_format( floatval( $item['supplement_price_usd'] ), 2 );
		}

		// Set Supplement Price - AUD.
		if ( ! empty( $item['supplement_price_aud'] ) ) {
			$data['meta_input']['supplement_price_aud'] = number_format( floatval( $item['supplement_price_aud'] ), 2 );
		}

		// Set Supplement Price - CAD.
		if ( ! empty( $item['supplement_price_cad'] ) ) {
			$data['meta_input']['supplement_price_cad'] = number_format( floatval( $item['supplement_price_cad'] ), 2 );
		}

		// Set Supplement Price - EUR.
		if ( ! empty( $item['supplement_price_eur'] ) ) {
			$data['meta_input']['supplement_price_eur'] = number_format( floatval( $item['supplement_price_eur'] ), 2 );
		}

		// Set Supplement Price - GBP.
		if ( ! empty( $item['supplement_price_gbp'] ) ) {
			$data['meta_input']['supplement_price_gbp'] = number_format( floatval( $item['supplement_price_gbp'] ), 2 );
		}

		// Set Itineraries.
		if ( ! empty( $item['itinerary_days_ids'] ) ) {
			$itinerary_days_ids = array_map( 'absint', explode( ',', strval( $item['itinerary_days_ids'] ) ) );
			$related_itinerary  = [];

			// Fetch related Itinerary.
			foreach ( $itinerary_days_ids as $itinerary_days_id ) {
				$itinerary = get_post_by_id( $itinerary_days_id, ITINERARY_DAY_POST_TYPE, 'drupal_iid' );

				// Check if post exists.
				if ( $itinerary instanceof WP_Post ) {
					$related_itinerary[] = $itinerary->ID;
				} else {
					WP_CLI::warning( 'Itinerary not found - ' . $itinerary_days_id );
				}
			}

			// Set related Itinerary.
			$data['meta_input']['itinerary_days'] = $related_itinerary;
		}

		// Set Inclusions.
		if ( ! empty( $item['inclusions_ids'] ) ) {
			$inclusions_ids    = array_map( 'absint', explode( ',', strval( $item['inclusions_ids'] ) ) );
			$related_inclusion = [];

			// Fetch related Inclusion.
			foreach ( $inclusions_ids as $inclusions_id ) {
				$inclusion = get_post_by_id( $inclusions_id, INCLUSION_SET_POST_TYPE );

				// Check if post exists.
				if ( $inclusion instanceof WP_Post ) {
					$related_inclusion[] = $inclusion->ID;
				} else {
					WP_CLI::warning( 'Inclusion not found - ' . $inclusions_id );
				}
			}

			// Set related Inclusion.
			$data['meta_input']['inclusions'] = $related_inclusion;
		}

		// Set Exclusions.
		if ( ! empty( $item['exclusions_ids'] ) ) {
			$exclusions_ids    = array_map( 'absint', explode( ',', strval( $item['exclusions_ids'] ) ) );
			$related_exclusion = [];

			// Fetch related Exclusion.
			foreach ( $exclusions_ids as $exclusions_id ) {
				$exclusion = get_post_by_id( $exclusions_id, EXCLUSION_SET_POST_TYPE );

				// Check if post exists.
				if ( $exclusion instanceof WP_Post ) {
					$related_exclusion[] = $exclusion->ID;
				} else {
					WP_CLI::warning( 'Exclusion not found - ' . $exclusions_id );
				}
			}

			// Set related Exclusion.
			$data['meta_input']['exclusions'] = $related_exclusion;
		}

		// Set TnC - Cancellation Policy.
		if ( ! empty( $item['tnc_cancellation_policy_id'] ) ) {
			$tnc_cancellation_policy = get_post_by_id( absint( $item['tnc_cancellation_policy_id'] ), POLICY_PAGE_POST_TYPE, 'drupal_tid' );

			// Check if post exist.
			if ( $tnc_cancellation_policy instanceof WP_Post ) {
				$data['meta_input']['tnc_cancellation_policy'] = $tnc_cancellation_policy->ID;
			}
		}

		// Set TnC - Terms and Conditions.
		if ( ! empty( $item['tnc_terms_and_conditions_id'] ) ) {
			$tnc_terms_and_conditions = get_post_by_id( absint( $item['tnc_terms_and_conditions_id'] ), POLICY_PAGE_POST_TYPE, 'drupal_tid' );

			// Check if post exist.
			if ( $tnc_terms_and_conditions instanceof WP_Post ) {
				$data['meta_input']['tnc_terms_and_conditions'] = $tnc_terms_and_conditions->ID;
			}
		}

		// TODO: Related Expedition to map with parent expedition.
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
			body.body_value AS post_content,
			body.body_summary AS post_excerpt,
			field_boilerplate.field_boilerplate_value AS boilerplate,
			field_softrip_package_id.field_softrip_package_id_value AS softrip_package_id,
			field_duration.field_duration_value AS duration_in_days,
			field_start_location.field_start_location_target_id AS start_location,
			field_end_location.field_end_location_target_id AS end_location,
			field_disembarkation_port.field_disembarkation_port_target_id AS embarkation_port,
			field_embarkation_port.field_embarkation_port_target_id AS disembarkation_port,
			field_media_collateral_item.field_media_collateral_item_target_id AS brochure_id,
			field_itinerary_map.field_itinerary_map_target_id AS map_id,
			field_mandatory_trf_price_aud.field_mandatory_trf_price_aud_number AS mandatory_transfer_price_aud,
			field_mandatory_trf_price_cad.field_mandatory_trf_price_cad_number AS mandatory_transfer_price_cad,
			field_mandatory_trf_price_eur.field_mandatory_trf_price_eur_number AS mandatory_transfer_price_eur,
			field_mandatory_trf_price_gbp.field_mandatory_trf_price_gbp_number AS mandatory_transfer_price_gbp,
			field_mandatory_trf_price_usd.field_mandatory_trf_price_usd_number AS mandatory_transfer_price_usd,
			field_mtp_exclusions.field_mtp_exclusions_target_id AS mtp_exclusion_id,
			field_mtp_inclusions.field_mtp_inclusions_target_id AS mtp_inclusion_id,
			field_supplement_price_aud.field_supplement_price_aud_number AS supplement_price_aud,
			field_supplement_price_cad.field_supplement_price_cad_number AS supplement_price_cad,
			field_supplement_price_eur.field_supplement_price_eur_number AS supplement_price_eur,
			field_supplement_price_gbp.field_supplement_price_gbp_number AS supplement_price_gbp,
			field_supplement_price_usd.field_supplement_price_usd_number AS supplement_price_usd,
			field_cancellation_policy.field_cancellation_policy_target_id AS tnc_cancellation_policy_id,
			field_itinerary_terms_conditions.field_itinerary_terms_conditions_target_id AS tnc_terms_and_conditions_id,
			field_commerce_tax_type.field_commerce_tax_type_target_id AS commerce_tax_type_id,
			field_parent_expedition.field_parent_expedition_target_id AS related_expedition_id,
			field_season.field_season_value AS season_value,
			(SELECT GROUP_CONCAT( field_itinerary_day_entities_target_id ORDER BY delta SEPARATOR ', ' ) FROM node__field_itinerary_day_entities AS itinerary_days WHERE node.nid = itinerary_days.entity_id AND itinerary_days.langcode = node.langcode) AS itinerary_days_ids,
			(SELECT GROUP_CONCAT( field_inclusions_target_id ORDER BY delta SEPARATOR ', ' ) FROM node__field_inclusions AS field_inclusions WHERE node.nid = field_inclusions.entity_id AND field_inclusions.langcode = node.langcode) AS inclusions_ids,
			(SELECT GROUP_CONCAT( field_exclusions_target_id ORDER BY delta SEPARATOR ', ' ) FROM node__field_exclusions AS field_exclusions WHERE node.nid = field_exclusions.entity_id AND field_exclusions.langcode = node.langcode) AS exclusions_ids
		FROM
			node
				LEFT JOIN node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
				LEFT JOIN node__body AS body ON node.nid = body.entity_id AND node.langcode = body.langcode
				LEFT JOIN node__field_boilerplate AS field_boilerplate ON node.nid = field_boilerplate.entity_id AND node.langcode = field_boilerplate.langcode
				LEFT JOIN node__field_start_location AS field_start_location ON node.nid = field_start_location.entity_id AND node.langcode = field_start_location.langcode
				LEFT JOIN node__field_end_location AS field_end_location ON node.nid = field_end_location.entity_id AND node.langcode = field_end_location.langcode
				LEFT JOIN node__field_cancellation_policy AS field_cancellation_policy ON node.nid = field_cancellation_policy.entity_id AND node.langcode = field_cancellation_policy.langcode
				LEFT JOIN node__field_commerce_tax_type AS field_commerce_tax_type ON node.nid = field_commerce_tax_type.entity_id AND node.langcode = field_commerce_tax_type.langcode
				LEFT JOIN node__field_disembarkation_port AS field_disembarkation_port ON node.nid = field_disembarkation_port.entity_id AND node.langcode = field_disembarkation_port.langcode
				LEFT JOIN node__field_duration AS field_duration ON node.nid = field_duration.entity_id AND node.langcode = field_duration.langcode
				LEFT JOIN node__field_embarkation_port AS field_embarkation_port ON node.nid = field_embarkation_port.entity_id AND node.langcode = field_embarkation_port.langcode
				LEFT JOIN node__field_itinerary_map AS field_itinerary_map ON node.nid = field_itinerary_map.entity_id AND node.langcode = field_itinerary_map.langcode
				LEFT JOIN node__field_itinerary_terms_conditions AS field_itinerary_terms_conditions ON node.nid = field_itinerary_terms_conditions.entity_id AND node.langcode = field_itinerary_terms_conditions.langcode
				LEFT JOIN node__field_mandatory_trf_price_aud AS field_mandatory_trf_price_aud ON node.nid = field_mandatory_trf_price_aud.entity_id AND node.langcode = field_mandatory_trf_price_aud.langcode
				LEFT JOIN node__field_mandatory_trf_price_cad AS field_mandatory_trf_price_cad ON node.nid = field_mandatory_trf_price_cad.entity_id AND node.langcode = field_mandatory_trf_price_cad.langcode
				LEFT JOIN node__field_mandatory_trf_price_eur AS field_mandatory_trf_price_eur ON node.nid = field_mandatory_trf_price_eur.entity_id AND node.langcode = field_mandatory_trf_price_eur.langcode
				LEFT JOIN node__field_mandatory_trf_price_gbp AS field_mandatory_trf_price_gbp ON node.nid = field_mandatory_trf_price_gbp.entity_id AND node.langcode = field_mandatory_trf_price_gbp.langcode
				LEFT JOIN node__field_mandatory_trf_price_usd AS field_mandatory_trf_price_usd ON node.nid = field_mandatory_trf_price_usd.entity_id AND node.langcode = field_mandatory_trf_price_usd.langcode
				LEFT JOIN node__field_media_collateral_item AS field_media_collateral_item ON node.nid = field_media_collateral_item.entity_id AND node.langcode = field_media_collateral_item.langcode
				LEFT JOIN node__field_mtp_exclusions AS field_mtp_exclusions ON node.nid = field_mtp_exclusions.entity_id AND node.langcode = field_mtp_exclusions.langcode
				LEFT JOIN node__field_mtp_inclusions AS field_mtp_inclusions ON node.nid = field_mtp_inclusions.entity_id AND node.langcode = field_mtp_inclusions.langcode
				LEFT JOIN node__field_parent_expedition AS field_parent_expedition ON node.nid = field_parent_expedition.entity_id AND node.langcode = field_parent_expedition.langcode
				LEFT JOIN node__field_season AS field_season ON node.nid = field_season.entity_id AND node.langcode = field_season.langcode
				LEFT JOIN node__field_softrip_package_id AS field_softrip_package_id ON node.nid = field_softrip_package_id.entity_id AND node.langcode = field_softrip_package_id.langcode
				LEFT JOIN node__field_supplement_price_aud AS field_supplement_price_aud ON node.nid = field_supplement_price_aud.entity_id AND node.langcode = field_supplement_price_aud.langcode
				LEFT JOIN node__field_supplement_price_cad AS field_supplement_price_cad ON node.nid = field_supplement_price_cad.entity_id AND node.langcode = field_supplement_price_cad.langcode
				LEFT JOIN node__field_supplement_price_eur AS field_supplement_price_eur ON node.nid = field_supplement_price_eur.entity_id AND node.langcode = field_supplement_price_eur.langcode
				LEFT JOIN node__field_supplement_price_gbp AS field_supplement_price_gbp ON node.nid = field_supplement_price_gbp.entity_id AND node.langcode = field_supplement_price_gbp.langcode
				LEFT JOIN node__field_supplement_price_usd AS field_supplement_price_usd ON node.nid = field_supplement_price_usd.entity_id AND node.langcode = field_supplement_price_usd.langcode
		WHERE
			node.type = 'itinerary';";

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
}
