<?php
/**
 * Migrate: Cabin Category.
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
use function Quark\Migration\Drupal\download_file_by_mid;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\CabinCategories\POST_TYPE;
use const Quark\CabinCategories\CABIN_CLASS_TAXONOMY;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;
use const Quark\ShipDecks\POST_TYPE as SHIP_DECK_POST_TYPE;

/**
 * Class Cabin Category.
 */
class Cabin_Category {

	/**
	 * Migrate all Cabin Category.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts(): void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch Cabin Category data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for "Cabin Category" post-type!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "Cabin Category" post-type', count( $data ) );

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
		WP_CLI::runcommand( 'term recount ' . CABIN_CLASS_TAXONOMY );
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
	 *     post_content : string,
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
		$nid          = ! empty( $item['nid'] ) ? absint( $item['nid'] ) : 0;
		$title        = '';
		$created_at   = gmdate( 'Y-m-d H:i:s' );
		$modified_at  = gmdate( 'Y-m-d H:i:s' );
		$status       = 'draft';
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
			'post_excerpt'      => trim( wp_strip_all_tags( strval( $item['post_excerpt'] ) ) ),
			'post_status'       => $status,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
			'meta_input'        => [],
		];

		// Set cabin name metadata.
		if ( ! empty( $item['cabin_name'] ) ) {
			$data['meta_input']['cabin_name'] = strval( qrk_sanitize_attribute( $item['cabin_name'] ) );
		}

		// Set cabin_category_id metadata.
		if ( ! empty( $item['cabin_category_id'] ) ) {
			$data['meta_input']['cabin_category_id'] = strval( $item['cabin_category_id'] );
		}

		// Set related_ship metadata.
		if ( ! empty( $item['ship_id'] ) ) {
			$related_ship = get_post_by_id( absint( $item['ship_id'] ), SHIP_POST_TYPE );

			// Check if post exists.
			if ( $related_ship instanceof WP_Post ) {
				$data['meta_input']['related_ship'] = $related_ship->ID;
			}
		}

		// Set Legend Color metadata.
		if ( ! empty( $item['legend_color_color'] ) ) {
			$data['meta_input']['legend_color'] = strval( qrk_sanitize_attribute( $item['legend_color_color'] ) );
		}

		// set cabin_category_size_range_from metadata.
		if ( ! empty( $item['cabin_category_size_range_from'] ) ) {
			$data['meta_input']['cabin_category_size_range_from'] = absint( $item['cabin_category_size_range_from'] );
		}

		// set cabin_category_size_range_to metadata.
		if ( ! empty( $item['cabin_category_size_range_to'] ) ) {
			$data['meta_input']['cabin_category_size_range_to'] = absint( $item['cabin_category_size_range_to'] );
		}

		// Set cabin_occupancy_pax_range_from metadata.
		if ( ! empty( $item['cabin_occupancy_pax_range_from'] ) ) {
			$data['meta_input']['cabin_occupancy_pax_range_from'] = absint( $item['cabin_occupancy_pax_range_from'] );
		}

		// Set cabin_occupancy_pax_range_to metadata.
		if ( ! empty( $item['cabin_occupancy_pax_range_to'] ) ) {
			$data['meta_input']['cabin_occupancy_pax_range_to'] = absint( $item['cabin_occupancy_pax_range_to'] );
		}

		// Set cabin_bed_configuration metadata.
		if ( ! empty( $item['cabin_bed_configuration'] ) ) {
			$data['meta_input']['cabin_bed_configuration'] = strval( qrk_sanitize_attribute( $item['cabin_bed_configuration'] ) );
		}

		// Set cabin_category_cabin_count metadata.
		if ( ! empty( $item['cabin_category_cabin_count'] ) ) {
			$data['meta_input']['cabin_category_cabin_count'] = absint( $item['cabin_category_cabin_count'] );
		}

		// Set low_inventory_threshold metadata.
		if ( ! empty( $item['low_inventory_threshold'] ) ) {
			$data['meta_input']['low_inventory_threshold'] = absint( $item['low_inventory_threshold'] );
		}

		// Set cabin_class term.
		if ( ! empty( $item['cabin_class_id'] ) ) {
			$cabin_class = get_term_by_id( absint( $item['cabin_class_id'] ), CABIN_CLASS_TAXONOMY );

			// Check if term exists.
			if ( $cabin_class instanceof WP_Term ) {
				$data['tax_input'][ CABIN_CLASS_TAXONOMY ][] = $cabin_class->term_id;
			}
		}

		// Set related_decks metadata.
		if ( ! empty( $item['ship_decks_target_ids'] ) ) {
			$related_deck_nids = array_map( 'absint', explode( ',', strval( $item['ship_decks_target_ids'] ) ) );
			$related_decks     = [];

			// Fetch related decks.
			foreach ( $related_deck_nids as $deck_nid ) {
				$deck = get_post_by_id( $deck_nid, SHIP_DECK_POST_TYPE );

				// Check if post exists.
				if ( $deck instanceof WP_Post ) {
					$related_decks[] = $deck->ID;
				}
			}

			// Set related_decks metadata.
			$data['related_decks'] = $related_decks;
		}

		// Set related_images metadata.
		if ( ! empty( $item['images_target_ids'] ) ) {
			$related_image_mids = array_map( 'absint', explode( ',', strval( $item['images_target_ids'] ) ) );
			$related_images     = [];

			// map drupal media with WordPress attachments.
			foreach ( $related_image_mids as $image_mid ) {
				$image = download_file_by_mid( $image_mid );

				// Check if image exists.
				if ( ! empty( $image ) ) {
					$related_images[] = $image;
				}
			}

			// Set related_images metadata.
			$data['meta_input']['cabin_images'] = $related_images;
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
			body.body_value AS post_content,
			body.body_summary AS post_excerpt,
			(SELECT GROUP_CONCAT( field_ship_decks_target_id ORDER BY delta SEPARATOR ', ' ) FROM `node__field_ship_decks` AS `field_ship_decks` WHERE node.nid = field_ship_decks.entity_id AND field_ship_decks.langcode = node.langcode) AS ship_decks_target_ids,
			(SELECT GROUP_CONCAT( field_images_target_id ORDER BY delta SEPARATOR ', ' ) FROM `node__field_images` AS `field_images` WHERE node.nid = field_images.entity_id AND field_images.langcode = node.langcode) AS images_target_ids,
			field_alternate_title.field_alternate_title_value AS cabin_name,
			field_cabin_bed_configuration.field_cabin_bed_configuration_value AS cabin_bed_configuration,
			field_cabin_category_cabin_count.field_cabin_category_cabin_count_value AS cabin_category_cabin_count,
			field_cabin_category_id.field_cabin_category_id_value AS cabin_category_id,
			field_cabin_category_size_range.field_cabin_category_size_range_from AS cabin_category_size_range_from,
			field_cabin_category_size_range.field_cabin_category_size_range_to AS cabin_category_size_range_to,
			field_cabin_class.field_cabin_class_target_id AS cabin_class_id,
			field_cabin_occupancy_pax_range.field_cabin_occupancy_pax_range_from AS cabin_occupancy_pax_range_from,
			field_cabin_occupancy_pax_range.field_cabin_occupancy_pax_range_to AS cabin_occupancy_pax_range_to,
			field_legend_color.field_legend_color_color AS legend_color_color,
			field_low_inventory_threshold.field_low_inventory_threshold_value AS low_inventory_threshold,
			field_ship.field_ship_target_id AS ship_id
		FROM
			node
				LEFT JOIN node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
				LEFT JOIN node__body AS body ON node.nid = body.entity_id AND node.langcode = body.langcode
				LEFT JOIN node__field_alternate_title AS field_alternate_title ON node.nid = field_alternate_title.entity_id AND node.langcode = field_alternate_title.langcode
				LEFT JOIN node__field_cabin_bed_configuration AS field_cabin_bed_configuration ON node.nid = field_cabin_bed_configuration.entity_id AND node.langcode = field_cabin_bed_configuration.langcode
				LEFT JOIN node__field_cabin_category_cabin_count AS field_cabin_category_cabin_count ON node.nid = field_cabin_category_cabin_count.entity_id AND node.langcode = field_cabin_category_cabin_count.langcode
				LEFT JOIN node__field_cabin_category_id AS field_cabin_category_id ON node.nid = field_cabin_category_id.entity_id AND node.langcode = field_cabin_category_id.langcode
				LEFT JOIN node__field_cabin_category_size_range AS field_cabin_category_size_range ON node.nid = field_cabin_category_size_range.entity_id AND node.langcode = field_cabin_category_size_range.langcode
				LEFT JOIN node__field_cabin_class AS field_cabin_class ON node.nid = field_cabin_class.entity_id AND node.langcode = field_cabin_class.langcode
				LEFT JOIN node__field_cabin_occupancy_pax_range AS field_cabin_occupancy_pax_range ON node.nid = field_cabin_occupancy_pax_range.entity_id AND node.langcode = field_cabin_occupancy_pax_range.langcode
				LEFT JOIN node__field_legend_color AS field_legend_color ON node.nid = field_legend_color.entity_id AND node.langcode = field_legend_color.langcode
				LEFT JOIN node__field_low_inventory_threshold AS field_low_inventory_threshold ON node.nid = field_low_inventory_threshold.entity_id AND node.langcode = field_low_inventory_threshold.langcode
				LEFT JOIN node__field_ship AS field_ship ON node.nid = field_ship.entity_id AND node.langcode = field_ship.langcode
		WHERE
			node.type = 'cabin_category'";

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
