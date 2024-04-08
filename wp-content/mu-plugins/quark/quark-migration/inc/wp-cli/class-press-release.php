<?php
/**
 * Migrate: Press Release.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use cli\progress\Bar;
use WP_CLI;
use WP_CLI\ExitException;

use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\get_post_by_id;
use function Quark\Migration\Drupal\prepare_content;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\PressRelease\POST_TYPE;

/**
 * Class Press_Release.
 */
class Press_Release {

	/**
	 * Migrate all Press Release.
	 *
	 * @subcommand posts
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts() : void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch testimonial data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for "press-release" post-type!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "press-release" post-type', count( $data ) );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Start inserting terms.
		foreach ( $data as $item ) {
			// Insert terms.
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
	public function insert_post( array $drupal_post = [] ) : void {
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
		if ( is_wp_error( $output ) ) {
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
	 *     post_type: string,
	 *     post_author: string,
	 *     post_title : string,
	 *     post_date : string,
	 *     post_date_gmt : string,
	 *     post_modified : string,
	 *     post_modified_gmt : string,
	 *     post_name: string,
	 *     post_content : string,
	 *     post_excerpt : string,
	 *     post_status : string,
	 *     comment_status: string,
	 *     ping_status: string,
	 *     meta_input : array{
	 *          drupal_id : int,
	 *     }
	 * }
	 */
	public function normalize_drupal_post( array $item = [] ) : array {
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
			// Remove wrapper div tags from content.
			$post_content = preg_replace( '/<\/?div[^>]*>/i', '', strval( $item['post_content'] ) );
		}

		// post excerpt.
		if ( ! empty( $item['post_excerpt'] ) && is_string( $item['post_excerpt'] ) ) {
			$post_excerpt = wp_strip_all_tags( trim( $item['post_excerpt'] ) );
		}

		// Post name.
		if ( ! empty( $item['drupal_url'] ) && is_string( $item['drupal_url'] ) ) {
			// remove /deprecated from url.
			$drupal_url = str_replace( '/deprecated', '', $item['drupal_url'] );

			/**
			 * Break the url into parts and use the last part as post name.
			 * i.e. - /press-releases/2013/12/quark-expeditions-celebrates-world-travel-award.
			 */
			$parts     = explode( '/', $drupal_url );
			$post_name = end( $parts );
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
			'post_content'      => prepare_content( strval( $post_content ) ),
			'post_excerpt'      => $post_excerpt,
			'post_status'       => $status,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
			'meta_input'        => [
				'drupal_id' => $nid,
			],
		];

		// SEO meta data.
		if ( ! empty( $item['field_metatags_value'] ) && is_string( $item['field_metatags_value'] ) ) {
			$seo_meta_data = maybe_unserialize( $item['field_metatags_value'] );

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
	public function get_drupal_data() : array {
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
			body.body_summary AS post_excerpt,
			field_metatags.field_metatags_value AS field_metatags_value,
			( SELECT alias AS drupal_url FROM path_alias WHERE path = CONCAT( '/node/', node.nid ) ORDER BY id DESC LIMIT 0, 1 ) AS drupal_url
		FROM
			node
			LEFT JOIN node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
			LEFT JOIN `node__body` AS `body` ON node.nid = body.entity_id AND node.langcode = body.langcode
			LEFT JOIN `node__field_metatags` AS `field_metatags` ON node.nid = field_metatags.entity_id AND node.langcode = field_metatags.langcode
		WHERE
			node.type = 'press_release'";

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
