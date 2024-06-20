<?php
/**
 * Migrate: Departure.
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

use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\get_post_by_id;
use function Quark\Migration\Drupal\get_term_by_id;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\Departures\POST_TYPE;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\StaffMembers\POST_TYPE as STAFF_MEMBER_POST_TYPE;
use const Quark\StaffMembers\DEPARTMENT_TAXONOMY;

/**
 * Class Departure.
 */
class Departure {

	/**
	 * Migrate all Departure.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts(): void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch Departure data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for "Departure" post-type!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "Departure" post-type', count( $data ) );

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
		} elseif (
			array_key_exists( 'related_decks', $normalized_post )
			&& $normalized_post['related_decks']
		) {
			update_field( 'related_decks', $normalized_post['related_decks'], $output );
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
	 *     post_status : string,
	 *     comment_status : string,
	 *     ping_status : string,
	 *     meta_input : array{
	 *         drupal_id : int,
	 *     },
	 *     related_decks ?: array<int>,
	 * }
	 */
	public function normalize_drupal_post( array $item = [] ): array {
		// Bail out if empty.
		if ( empty( $item ) ) {
			return [];
		}

		// Normalize data.
		$nid         = ! empty( $item['nid'] ) ? absint( $item['nid'] ) : 0;
		$title       = '';
		$created_at  = gmdate( 'Y-m-d H:i:s' );
		$modified_at = gmdate( 'Y-m-d H:i:s' );
		$status      = 'draft';

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

		// Prepare post data.
		$data = [
			'post_type'         => POST_TYPE,
			'post_author'       => '1',
			'post_title'        => $title,
			'post_date'         => $created_at,
			'post_date_gmt'     => $created_at,
			'post_modified'     => $modified_at,
			'post_modified_gmt' => $modified_at,
			'post_status'       => $status,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
			'meta_input'        => [],
		];

		// Set departure id.
		if ( ! empty( $item['departure_id'] ) ) {
			$data['meta_input']['departure_id'] = strval( $item['departure_id'] );
		}

		// Set package id.
		if ( ! empty( $item['package_id'] ) ) {
			$data['meta_input']['package_id'] = strval( $item['package_id'] );
		}

		// Set departure start date.
		if ( ! empty( $item['departure_start_date'] ) ) {
			$data['meta_input']['departure_start_date'] = strval( $item['departure_start_date'] );
		}

		// Set departure end date.
		if ( ! empty( $item['departure_end_date'] ) ) {
			$data['meta_input']['departure_end_date'] = strval( $item['departure_end_date'] );
		}

		// Set duration.
		if ( ! empty( $item['duration'] ) ) {
			$data['meta_input']['duration'] = absint( $item['duration'] );
		}

		// Set spoken_language_ids.
		if ( ! empty( $item['spoken_language_ids'] ) && is_string( $item['spoken_language_ids'] ) ) {
			$spoken_language = array_map( 'absint', explode( ',', $item['spoken_language_ids'] ) );

			// Set spoken_language_ids.
			foreach ( $spoken_language as $lang_id ) {
				$term = get_term_by_id( $lang_id, SPOKEN_LANGUAGE_TAXONOMY );

				// Check if term is instance of WP_Term.
				if ( $term instanceof WP_Term ) {
					$data['tax_input'][ SPOKEN_LANGUAGE_TAXONOMY ][] = $term->term_id;
				}
			}
		}

		// Set departure_staff_members_id.
		if ( ! empty( $item['departure_staff_members_id'] ) ) {
			$departure_staff_members_data = $this->get_staff_member_paragraph_data( absint( $item['departure_staff_members_id'] ) );

			// Check if data is not empty.
			if ( ! empty( $departure_staff_members_data ) ) {
				$staff_members_count = 0;

				// Loop through staff members.
				foreach ( $departure_staff_members_data as $staff_member ) {
					// Check if staff member is special guest.
					if ( ! empty( $staff_member['is_special_guest'] ) && 1 === absint( $staff_member['is_special_guest'] ) ) {
						$data['meta_input'][ 'expedition_team_' . $staff_members_count . '_is_special_guest' ] = true;
					}

					// Set staff member id.
					if ( ! empty( $staff_member['staff_member_id'] ) ) {
						$staff_member_id = get_post_by_id( absint( $staff_member['staff_member_id'] ), STAFF_MEMBER_POST_TYPE );

						// Check if staff member is instance of WP_Post.
						if ( $staff_member_id instanceof WP_Post ) {
							$data['meta_input'][ 'expedition_team_' . $staff_members_count . '_staff_member' ] = $staff_member_id->ID;
						}
					}

					// Set staff role id.
					if ( ! empty( $staff_member['staff_role_id'] ) && is_string( $staff_member['staff_role_id'] ) ) {
						$staff_member_ids  = array_map( 'absint', explode( ',', $staff_member['staff_role_id'] ) );
						$staff_member_tids = [];

						// Set staff role id.
						foreach ( $staff_member_ids as $staff_member_id ) {
							$term = get_term_by_id( $staff_member_id, DEPARTMENT_TAXONOMY );

							// Check if term is instance of WP_Term.
							if ( $term instanceof WP_Term ) {
								$staff_member_tids[] = $term->term_id;
							}
						}

						// Set staff role id.
						$data['meta_input'][ 'expedition_team_' . $staff_members_count . '_departure_staff_role' ] = $staff_member_tids;
					}

					// Increment staff members count.
					++$staff_members_count;
				}

				// Set staff members.
				$data['meta_input']['expedition_team'] = $staff_members_count;
			}
		}

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
			field_departure_id.field_departure_id_value AS departure_id,
			field_softrip_package_id.field_softrip_package_id_value AS package_id,
			field_departure_start_date.field_departure_start_date_value AS departure_start_date,
			field_departure_end_date.field_departure_end_date_value AS departure_end_date,
			field_duration.field_duration_value AS duration,
			field_departure_staff_members.field_departure_staff_members_target_id AS departure_staff_members_id,
			(SELECT GROUP_CONCAT( field_departure_languages_target_id ORDER BY delta SEPARATOR ', ' ) FROM node__field_departure_languages AS spoken_languages WHERE node.nid = spoken_languages.entity_id AND spoken_languages.langcode = node.langcode) AS spoken_language_ids
		FROM
			node
				LEFT JOIN node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
				LEFT JOIN node__field_departure_end_date AS field_departure_end_date ON node.nid = field_departure_end_date.entity_id AND node.langcode = field_departure_end_date.langcode
				LEFT JOIN node__field_departure_id AS field_departure_id ON node.nid = field_departure_id.entity_id AND node.langcode = field_departure_id.langcode
				LEFT JOIN node__field_departure_start_date AS field_departure_start_date ON node.nid = field_departure_start_date.entity_id AND node.langcode = field_departure_start_date.langcode
				LEFT JOIN node__field_duration AS field_duration ON node.nid = field_duration.entity_id AND node.langcode = field_duration.langcode
				LEFT JOIN node__field_softrip_package_id AS field_softrip_package_id ON node.nid = field_softrip_package_id.entity_id AND node.langcode = field_softrip_package_id.langcode
				LEFT JOIN node__field_departure_staff_members AS field_departure_staff_members ON node.nid = field_departure_staff_members.entity_id AND node.langcode = field_departure_staff_members.langcode
		WHERE
			node.type = 'departure'";

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
	 * Fetch Staff member paragraph data from drupal database.
	 *
	 * @param int $departure_id Departure id.
	 *
	 * @return array{}|array<int, array<string, int|string>> Drupal data.
	 */
	public function get_staff_member_paragraph_data( int $departure_id = 0 ): array {
		// Bail out if departure id is empty.
		if ( empty( $departure_id ) ) {
			return [];
		}

		// Get database connection.
		$drupal_database = get_database();

		// Query.
		$query = "SELECT
			paragraphs.id,
			staff_roles.field_departure_staff_role_target_id as staff_role_id,
			is_special_guest.field_is_special_guest_value as is_special_guest,
			staff_member.field_staff_member_target_id as staff_member_id
		FROM
			paragraphs_item_field_data AS paragraphs
			LEFT JOIN paragraph__field_departure_staff_role AS staff_roles ON paragraphs.id = staff_roles.entity_id
			LEFT JOIN paragraph__field_is_special_guest AS is_special_guest ON paragraphs.id = is_special_guest.entity_id
			LEFT JOIN paragraph__field_staff_member AS staff_member ON paragraphs.id = staff_member.entity_id
		WHERE
			paragraphs.langcode = 'en' AND paragraphs.id = %d";

		// Fetch data.
		$result = $drupal_database->get_results( $drupal_database->prepare( $query, $departure_id ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			// Bail out.
			return [];
		}

		// Return data.
		return $result;
	}
}
