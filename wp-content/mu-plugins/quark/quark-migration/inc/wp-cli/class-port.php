<?php
/**
 * Migrate: Port Taxonomy from Drupal to WordPress CPT.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use cli\progress\Bar;
use WP_CLI;
use WP_Error;
use WP_CLI\ExitException;

use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\get_post_by_id;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\Ports\POST_TYPE;

/**
 * Class Port.
 */
class Port {

	/**
	 * Migrate all Ports.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts(): void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch ports data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for port!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "port" post-type', count( $data ) );

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
		$wp_post = get_post_by_id( $normalized_post['meta_input']['drupal_tid'], POST_TYPE, 'drupal_tid' );

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
			WP_CLI::warning( 'Unable to insert/update port - ' . $normalized_post['meta_input']['drupal_tid'] );
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
	 *     post_title : string,
	 *     post_date : string,
	 *     post_date_gmt : string,
	 *     post_modified : string,
	 *     post_modified_gmt : string,
	 *     post_name: string,
	 *     post_content : string,
	 *     post_status : string,
	 *     comment_status: string,
	 *     ping_status: string,
	 *     meta_input : array{
	 *          drupal_tid : int,
	 *     }
	 * }
	 */
	public function normalize_drupal_post( array $item = [] ): array {
		// Bail out if empty.
		if ( empty( $item ) ) {
			return [];
		}

		// Normalize data.
		$nid          = ! empty( $item['tid'] ) ? absint( $item['tid'] ) : 0;
		$title        = '';
		$created_at   = gmdate( 'Y-m-d H:i:s' );
		$modified_at  = gmdate( 'Y-m-d H:i:s' );
		$status       = 'draft';
		$post_content = '';
		$post_name    = '';

		// Title.
		if ( is_string( $item['name'] ) && ! empty( $item['name'] ) ) {
			$title = trim( $item['name'] );
		}

		// Modified date.
		if ( ! empty( $item['changed'] ) ) {
			$created_at  = gmdate( 'Y-m-d H:i:s', absint( $item['changed'] ) );
			$modified_at = $created_at;
		}

		// Status.
		if ( ! empty( $item['status'] ) && 1 === absint( $item['status'] ) ) {
			$status = 'publish';
		}

		// post content.
		if ( ! empty( $item['description__value'] ) ) {
			$post_content = strval( $item['description__value'] );
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
			'post_status'       => $status,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
			'meta_input'        => [
				'drupal_tid' => $nid,
			],
		];

		// Set latitude metadata.
		if ( ! empty( $item['field_geocoordinates_lat'] ) ) {
			$data['meta_input']['latitude'] = $item['field_geocoordinates_lat'];
		}

		// Set longitude metadata.
		if ( ! empty( $item['field_geocoordinates_lng'] ) ) {
			$data['meta_input']['longitude'] = $item['field_geocoordinates_lng'];
		}

		// Set Port Code metadata.
		if ( ! empty( $item['field_port_code_value'] ) ) {
			$data['meta_input']['port_code'] = $item['field_port_code_value'];
		}

		// Set country metadata.
		if ( ! empty( $item['field_port_address_country_code'] ) ) {
			$data['meta_input']['country'] = $item['field_port_address_country_code'];
		}

		// Set locality metadata.
		if ( ! empty( $item['field_port_address_locality'] ) ) {
			$data['meta_input']['locality'] = $item['field_port_address_locality'];
		}

		// Set Administrative area metadata.
		if ( ! empty( $item['field_port_address_administrative_area'] ) ) {
			$data['meta_input']['administrative_area'] = $item['field_port_address_administrative_area'];
		}

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
			term.`tid`,
			field_data.`name`,
			field_data.`description__value`,
			field_data.`status`,
			field_data.`changed`,
			field_geocoordinates.field_geocoordinates_lat AS `field_geocoordinates_lat`,
			field_geocoordinates.field_geocoordinates_lng AS `field_geocoordinates_lng`,
			field_port_address.field_port_address_country_code AS `field_port_address_country_code`,
			field_port_address.field_port_address_administrative_area AS `field_port_address_administrative_area`,
			field_port_address.field_port_address_locality AS `field_port_address_locality`,
			field_port_code.field_port_code_value AS `field_port_code_value`
		FROM
			taxonomy_term_data AS term
			LEFT JOIN taxonomy_term__parent AS parent ON term.`tid` = parent.`entity_id` AND term.langcode = parent.langcode
			LEFT JOIN taxonomy_term_field_data AS field_data ON term.`tid` = field_data.`tid` AND term.langcode = field_data.langcode
			LEFT JOIN `taxonomy_term__field_geocoordinates` AS `field_geocoordinates` ON term.tid = field_geocoordinates.entity_id AND term.langcode = field_geocoordinates.langcode
			LEFT JOIN `taxonomy_term__field_port_address` AS `field_port_address` ON term.tid = field_port_address.entity_id AND term.langcode = field_port_address.langcode
			LEFT JOIN `taxonomy_term__field_port_code` AS `field_port_code` ON term.tid = field_port_code.entity_id AND term.langcode = field_port_code.langcode
		WHERE
			term.`vid` = 'ports'
		ORDER BY
			parent.`parent_target_id` ASC;";

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
