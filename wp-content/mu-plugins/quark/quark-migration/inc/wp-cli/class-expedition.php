<?php
/**
 * Migrate: Expedition.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use cli\progress\Bar;
use WP_CLI;
use WP_Term;
use WP_Error;
use WP_CLI\ExitException;

use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\get_post_by_id;
use function Quark\Migration\Drupal\get_term_by_id;
use function Quark\Migration\Drupal\prepare_content;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\Expeditions\POST_TYPE;
use const Quark\Expeditions\EXPEDITION_CATEGORY_TAXONOMY;
use const Quark\Expeditions\DESTINATION_TAXONOMY;

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
			$seo_meta_data = json_decode( $item['metatags'], true );

			// Check if data is array.
			if ( is_array( $seo_meta_data ) ) {
				$search_for   = [
					'[node:title]',
					'→',
					'|',
					'[site:name]',
					'[current-page:page-number]',
					'[current-page:pager]',
				];
				$replace_with = [
					'%%title%%',
					'%%sep%%',
					'%%sep%%',
					'%%sitename%%',
					'%%page%%',
					'',
				];

				// Process seo meta title for WP SEO plugin.
				if ( ! empty( $seo_meta_data['title']['value'] ) ) {
					$data['meta_input']['_yoast_wpseo_title'] = str_replace(
						$search_for,
						$replace_with,
						trim( $seo_meta_data['title']['value'] )
					);
				}

				// Process seo meta description for WP SEO plugin.
				if ( ! empty( $seo_meta_data['description']['value'] ) ) {
					$data['meta_input']['_yoast_wpseo_metadesc'] = str_replace(
						$search_for,
						$replace_with,
						trim( $seo_meta_data['description']['value'] )
					);
				}

				// Process SEO robots tags for WP SEO plugin.
				if ( ! empty( $seo_meta_data['robots'] ) && is_string( $seo_meta_data['robots'] ) ) {
					$data['meta_input']['_yoast_wpseo_meta-robots-adv'] = str_replace( ' ', '', $seo_meta_data['robots'] );
				}
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
			( SELECT count(1) FROM redirect WHERE redirect_source__path = CONCAT( 'node/', node.nid ) ) AS is_redirected,
			( SELECT alias AS drupal_url FROM path_alias WHERE path = CONCAT( '/node/', node.nid ) ORDER BY id DESC LIMIT 0, 1 ) AS drupal_url,
			body.body_value AS post_content,
			body.body_summary AS post_excerpt,
			field_expedition_category.field_expedition_category_target_id AS expedition_category_id,
			field_metatags.field_metatags_value AS metatags,
			field_primary_destination.field_primary_destination_target_id AS primary_destination_id,
			(SELECT GROUP_CONCAT( field_destinations_target_id ORDER BY delta SEPARATOR ', ' ) FROM node__field_destinations AS field_destinations WHERE node.nid = field_destinations.entity_id AND field_destinations.langcode = node.langcode) AS destination_ids
		FROM
			node
				LEFT JOIN node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
				LEFT JOIN node__body AS body ON node.nid = body.entity_id AND node.langcode = body.langcode
				LEFT JOIN node__field_expedition_category AS field_expedition_category ON node.nid = field_expedition_category.entity_id AND node.langcode = field_expedition_category.langcode
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
}
