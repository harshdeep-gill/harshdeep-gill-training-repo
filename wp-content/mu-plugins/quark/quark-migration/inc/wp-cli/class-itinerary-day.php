<?php
/**
 * Migrate: Itinerary Days nodes from Drupal to WordPress CPT.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use cli\progress\Bar;
use WP_CLI;
use WP_Error;
use WP_CLI\ExitException;
use WP_Post;

use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\get_post_by_id;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\ItineraryDays\POST_TYPE;
use const Quark\Ports\POST_TYPE as PORT_POST_TYPE;

/**
 * Class Itinerary_Day.
 */
class Itinerary_Day {

	/**
	 * Migrate all Itinerary Days.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts(): void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch Itinerary Days data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for Itinerary Days!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "Itinerary Day" post-type', count( $data ) );

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
		$wp_post = get_post_by_id( $normalized_post['meta_input']['drupal_iid'], POST_TYPE, 'drupal_iid' );

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
			WP_CLI::warning( 'Unable to insert/update Itinerary Day - ' . $normalized_post['meta_input']['drupal_iid'] );
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
	 *     post_status: string,
	 *     comment_status: string,
	 *     ping_status: string,
	 *     meta_input: array{
	 *          drupal_iid : int,
	 *     }
	 * }
	 */
	public function normalize_drupal_post( array $item = [] ): array {
		// Bail out if empty.
		if ( empty( $item ) ) {
			return [];
		}

		// Normalize data.
		$iid          = ! empty( $item['iid'] ) ? absint( $item['iid'] ) : 0;
		$title        = '';
		$created_at   = gmdate( 'Y-m-d H:i:s' );
		$modified_at  = gmdate( 'Y-m-d H:i:s' );
		$status       = 'draft';
		$post_content = '';
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
				'drupal_iid' => $iid,
			],
		];

		// Set Day title.
		if ( ! empty( $item['day_title'] ) ) {
			$data['meta_input']['day_title'] = strval( qrk_sanitize_attribute( $item['day_title'] ) );
		}

		// Set Day number - From.
		if ( ! empty( $item['day_number_from'] ) ) {
			$data['meta_input']['day_number_from'] = absint( $item['day_number_from'] );
		}

		// Set Day number - To.
		if ( ! empty( $item['day_number_to'] ) ) {
			$data['meta_input']['day_number_to'] = absint( $item['day_number_to'] );
		}

		// Set Location.
		if ( ! empty( $item['location'] ) ) {
			$data['meta_input']['location'] = strval( qrk_sanitize_attribute( $item['location'] ) );
		}

		// Set Port.
		if ( ! empty( $item['port_id'] ) ) {
			$port = get_post_by_id( absint( $item['port_id'] ), PORT_POST_TYPE, 'drupal_tid' );

			// Check if port exists.
			if ( $port instanceof WP_Post ) {
				$data['meta_input']['port'] = $port->ID;
			}
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
			itinerary_day.id as iid,
			itinerary_day.title,
			itinerary_day.status,
			itinerary_day.description__value as post_content,
			itinerary_day.created,
			itinerary_day.changed,
			title.field_itin_day_title_value as day_title,
			day_number.field_itin_day_day_number_range_from as day_number_from,
			day_number.field_itin_day_day_number_range_to as day_number_to,
			location.field_itin_day_location_value as location,
			port.field_itin_day_port_target_id as port_id
		FROM
			itinerary_day_field_data as itinerary_day
				LEFT JOIN itinerary_day__field_itin_day_title as title ON itinerary_day.id = title.entity_id AND title.langcode = itinerary_day.langcode
				LEFT JOIN itinerary_day__field_itin_day_day_number_range as day_number ON itinerary_day.id = day_number.entity_id AND day_number.langcode = itinerary_day.langcode
				LEFT JOIN itinerary_day__field_itin_day_location as location ON itinerary_day.id = location.entity_id AND location.langcode = itinerary_day.langcode
				LEFT JOIN itinerary_day__field_itin_day_port as port ON itinerary_day.id = port.entity_id AND port.langcode = itinerary_day.langcode
		WHERE
			itinerary_day.langcode = 'en'";

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
