<?php
/**
 * Migrate: Ship Deck.
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
use function Quark\Migration\Drupal\download_file_by_mid;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\ShipDecks\POST_TYPE;

/**
 * Class Ship_Deck.
 */
class Ship_Deck {

	/**
	 * Migrate all Ship Decks.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts(): void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch Ship Decks data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for "Ship Deck" post-type!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "Ship Deck" post-type', count( $data ) );

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

		// Title.
		if ( is_string( $item['title'] ) && ! empty( $item['title'] ) ) {
			$title = strval( qrk_sanitize_attribute( trim( $item['title'] ) ) );
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
			'post_title'        => $title,
			'post_date'         => $created_at,
			'post_date_gmt'     => $created_at,
			'post_modified'     => $modified_at,
			'post_modified_gmt' => $modified_at,
			'post_content'      => $post_content,
			'post_status'       => $status,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
			'meta_input'        => [],
		];

		// Set deck name metadata.
		if ( ! empty( $item['deck_name'] ) ) {
			$data['meta_input']['deck_name'] = strval( qrk_sanitize_attribute( $item['deck_name'] ) );
		}

		// Set deck plan image metadata.
		if ( ! empty( $item['deck_plan_image'] ) ) {
			// Get attachment id.
			$deck_plan_image_id = download_file_by_mid( absint( $item['deck_plan_image'] ) );

			// Check if attachment exists.
			if ( ! $deck_plan_image_id instanceof WP_Error ) {
				$data['meta_input']['deck_plan_image'] = $deck_plan_image_id;
			}
		}

		// Set deck plan image metadata.
		if ( ! empty( $item['vertical_deck_plan_image'] ) ) {
			// Get attachment id.
			$vertical_deck_plan_id = download_file_by_mid( absint( $item['vertical_deck_plan_image'] ) );

			// Check if attachment exists.
			if ( ! $vertical_deck_plan_id instanceof WP_Error ) {
				$data['meta_input']['vertical_deck_plan_image'] = $vertical_deck_plan_id;
			}
		}

		// get Public spaces data.
		$public_spaces = $this->get_public_space_meta( $nid );

		// prepare public spaces data.
		if ( ! empty( $public_spaces ) ) {
			$public_spaces_count = 0;

			// Prepare public space data.
			foreach ( $public_spaces as $public_space ) {
				$title_meta_key = sprintf( 'public_spaces_%d_title', $public_space['delta'] );
				$desc_meta_key  = sprintf( 'public_spaces_%d_description', $public_space['delta'] );
				$image_meta_key = sprintf( 'public_spaces_%d_image', $public_space['delta'] );

				// Get image attachment id.
				$public_space_image_id = download_file_by_mid( absint( $public_space['field_feature_image_target_id'] ) );

				// Check if image exists.
				if ( ! $public_space_image_id instanceof WP_Error ) {
					$data['meta_input'][ $image_meta_key ] = $public_space_image_id;
				}

				// Set public space metadata.
				$data['meta_input'][ $title_meta_key ] = strval( qrk_sanitize_attribute( $public_space['field_feature_title_value'] ) );
				$data['meta_input'][ $desc_meta_key ]  = strval( qrk_sanitize_attribute( $public_space['field_feature_description_value'] ) );

				// Increment $public_spaces_count.
				++$public_spaces_count;
			}

			// Set public spaces count.
			$data['meta_input']['public_spaces'] = $public_spaces_count;
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
			field_data.publish_on,
			field_data.unpublish_on,
			body.body_value AS post_content,
			field_alternate_title.field_alternate_title_value AS deck_name,
			field_deck_plan.field_deck_plan_target_id AS deck_plan_image,
			field_vertical_deck_plan.field_vertical_deck_plan_target_id AS vertical_deck_plan_image
		FROM
			node
				LEFT JOIN node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
				LEFT JOIN `node__body` AS `body` ON node.nid = body.entity_id AND node.langcode = body.langcode
				LEFT JOIN `node__field_alternate_title` AS `field_alternate_title` ON node.nid = field_alternate_title.entity_id AND node.langcode = field_alternate_title.langcode
				LEFT JOIN `node__field_deck_plan` AS `field_deck_plan` ON node.nid = field_deck_plan.entity_id AND node.langcode = field_deck_plan.langcode
				LEFT JOIN `node__field_vertical_deck_plan` AS `field_vertical_deck_plan` ON node.nid = field_vertical_deck_plan.entity_id AND node.langcode = field_vertical_deck_plan.langcode
		WHERE
			node.type = 'ship_deck';";

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
	 * Get public space data.
	 *
	 * @param int $nid Node ID.
	 *
	 * @return array{}|array<int, array<string, int|string>> Drupal data.
	 */
	public function get_public_space_meta( int $nid = 0 ): array {
		// Get database connection.
		$drupal_database = get_database();

		// Query.
		$query = "SELECT
			field_deck_amenities.entity_id,
			field_deck_amenities.delta,
			field_deck_amenities_target_id AS deck_amenities_id,
			field_feature_description.field_feature_description_value,
			field_feature_image.field_feature_image_target_id,
			field_feature_title.field_feature_title_value
		FROM
			node__field_deck_amenities AS field_deck_amenities
			LEFT JOIN paragraphs_item_field_data AS paragraphs ON paragraphs.id = field_deck_amenities.field_deck_amenities_target_id and parent_type = 'node'
			LEFT JOIN paragraph__field_feature_description AS field_feature_description ON field_feature_description.entity_id = field_deck_amenities.field_deck_amenities_target_id
			LEFT JOIN paragraph__field_feature_image AS field_feature_image ON field_feature_image.entity_id = field_deck_amenities.field_deck_amenities_target_id
			LEFT JOIN paragraph__field_feature_title AS field_feature_title ON field_feature_title.entity_id = field_deck_amenities.field_deck_amenities_target_id
		WHERE
			 field_deck_amenities.entity_id = %d
		ORDER BY
			field_deck_amenities.delta";

		// Fetch data.
		$result = $drupal_database->get_results( $drupal_database->prepare( $query, $nid ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			// Bail out.
			return [];
		}

		// Return data.
		return $result;
	}
}
