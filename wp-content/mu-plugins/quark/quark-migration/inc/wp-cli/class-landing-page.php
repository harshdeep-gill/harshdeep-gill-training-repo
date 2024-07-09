<?php
/**
 * Migrate: Landing Page.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use cli\progress\Bar;
use WP_CLI;
use WP_Error;
use WP_CLI\ExitException;
use Quark\Migration\Drupal\Block_Converter;

use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\get_post_by_id;
use function Quark\Migration\Drupal\prepare_content;
use function Quark\Migration\Drupal\prepare_seo_data;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\Pages\POST_TYPE;

/**
 * Class Landing_Page.
 */
class Landing_Page {

	/**
	 * Drupal Node ids of Spacial pages like Home, contact us which will not be in Landing page CPT.
	 *
	 * @var array|int[]
	 */
	private array $spacial_pages = [
		102236,
		103191,
		103196,
		103201,
		103186,
		102696,
	];

	/**
	 * Migrate all Landing_Page.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts(): void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch Landing Page data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for "Landing Page" post-type!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "Landing Page" post-type', count( $data ) );

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

			// Skip special pages.
			if ( in_array( absint( $item['nid'] ), $this->spacial_pages, true ) ) {
				continue;
			}

			// Insert post.
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
	 *     post_excerpt : string,
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
		$post_excerpt = '';

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
			$post_content = prepare_content( strval( $item['post_content'] ) );
		}

		// Post excerpt.
		if ( ! empty( $item['post_excerpt'] ) && is_string( $item['post_excerpt'] ) ) {
			$post_excerpt = strval( qrk_sanitize_attribute( $item['post_excerpt'] ) );
		}

		// Get Hero banner block content.
		if ( ! empty( $item['hero_banner_id'] ) ) {
			$block = $this->get_drupal_hero_banner_data( absint( $item['hero_banner_id'] ) );

			// Convert block content.
			if ( ! empty( $block ) ) {
				$block_converter = new Block_Converter();
				$block_content   = $block_converter->get_drupal_block_data( $block );
			}

			// Check if hero banner exists.
			if ( ! empty( $block_content ) ) {
				$post_content = $block_content . $post_content;
			}
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

		// Set migrate_to_qq to metadata.
		if ( ! empty( $item['migrate_to_qq'] ) && 1 === absint( $item['migrate_to_qq'] ) ) {
			$data['meta_input']['migrate_to_ship_portal'] = 1;
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
			field_hero_banner.field_hero_banner_target_id AS hero_banner_id,
			field_metatags.field_metatags_value AS metatags,
			field_migrate_to_qq.field_migrate_to_qq_value AS migrate_to_qq
		FROM
			node
				LEFT JOIN node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
				LEFT JOIN node__body AS body ON node.nid = body.entity_id AND node.langcode = body.langcode
				LEFT JOIN node__field_hero_banner AS field_hero_banner ON node.nid = field_hero_banner.entity_id AND node.langcode = field_hero_banner.langcode
				LEFT JOIN node__field_metatags AS field_metatags ON node.nid = field_metatags.entity_id AND node.langcode = field_metatags.langcode
				LEFT JOIN node__field_migrate_to_qq AS field_migrate_to_qq ON node.nid = field_migrate_to_qq.entity_id AND node.langcode = field_migrate_to_qq.langcode
		WHERE
			node.type = 'landing_page'";

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
	 * Get drupal paragraph data.
	 *
	 * @param int $pid Drupal paragraph id.
	 *
	 * @return array{}|array<int|string, string|int>
	 */
	public function get_drupal_hero_banner_data( int $pid = 0 ): array {
		// Get database connection.
		$drupal_database = get_database();

		// Query.
		$query = "SELECT
			id,
			type,
			behavior_settings,
			langcode
		FROM
			paragraphs_item_field_data
		WHERE
			id = %s AND langcode = 'en';";

		// Fetch data.
		$result = $drupal_database->get_row( $drupal_database->prepare( $query, $pid ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch drupal_hero_banner_block data!' );

			// Bail out.
			return [];
		}

		// Return data.
		return $result;
	}
}
