<?php
/**
 * Migrate: Policy Pages.
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
use function Quark\Migration\Drupal\prepare_content;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\PolicyPages\POST_TYPE;

/**
 * Class Policy_Pages.
 */
class Policy_Pages {

	/**
	 * Special page IDs.
	 *
	 * @var array<int>
	 */
	private $special_pages = [
		'protection-promise' => 105721,
	];

	/**
	 * Migrate all Policy Pages.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts(): void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch policy pages data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for "policy page(agreement)" post-type!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "policy pages(agreement)" post-type', count( $data ) );

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
		// Skip if its special page.
		if ( ! empty( $drupal_post['nid'] ) && in_array( absint( $drupal_post['nid'] ), $this->special_pages, true ) ) {
			WP_CLI::line( 'Skipping special page: ' . $drupal_post['nid'] );

			// Bail out.
			return;
		}

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

		// post excerpt.
		if ( ! empty( $item['post_excerpt'] ) && is_string( $item['post_excerpt'] ) ) {
			$post_excerpt = wp_strip_all_tags( trim( $item['post_excerpt'] ) );
		}

		// Post name.
		if ( ! empty( $item['drupal_url'] ) && is_string( $item['drupal_url'] ) ) {
			/**
			 * Break the url into parts and use the last part as post name.
			 * i.e. - /terms-and-conditions/15-15promo → 15-15promo
			 */
			$parts     = explode( '/', $item['drupal_url'] );
			$post_name = end( $parts );
		}

		// TODO: Prepend hero banner as custom block.
		$post_content = prepare_content( strval( $item['post_content'] ) );

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

		// SEO meta data.
		if ( ! empty( $item['metatags'] ) && is_string( $item['metatags'] ) ) {
			$seo_meta_data = maybe_unserialize( $item['metatags'] );

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

		// Set alternate title as ACF field.
		if ( ! empty( $item['alternate_title'] ) && is_string( $item['alternate_title'] ) ) {
			$data['meta_input']['alternate_title'] = $item['alternate_title'];
		}

		// Set agreement type as ACF field.
		if ( ! empty( $item['agreement_type'] ) && is_string( $item['agreement_type'] ) ) {
			$data['meta_input']['agreement_type'] = $item['agreement_type'];
		}

		// Set agreement summary as ACF field.
		if ( ! empty( $item['agreement_summary'] ) && is_string( $item['agreement_summary'] ) ) {
			$data['meta_input']['marketing_option_summary'] = $item['agreement_summary'];
		}

		// Set migrate to ship portal as ACF field.
		if ( ! empty( $item['migrate_to_ship_portal'] ) && 1 === absint( $item['migrate_to_ship_portal'] ) ) {
			$data['meta_input']['migrate_to_ship_portal'] = '1';
		}

		// Set icon as ACF field.
		if ( ! empty( $item['icon_target_id'] ) && is_numeric( $item['icon_target_id'] ) ) {
			$icon = get_post_by_id( drupal_id: absint( $item['icon_target_id'] ), post_type: 'attachment', meta_key: 'drupal_fid' );

			// Check if icon is not empty.
			if ( $icon ) {
				$data['meta_input']['icon'] = $icon;
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
			node.nid,
			field_data.status,
			field_data.title,
			field_data.created,
			field_data.changed,
			( SELECT alias AS drupal_url FROM path_alias WHERE path = CONCAT( '/node/', node.nid ) ORDER BY id DESC LIMIT 0, 1 ) AS drupal_url,
			body.body_value AS post_content,
			body.body_summary AS post_excerpt,
			field_agreement_summary.field_agreement_summary_value AS agreement_summary,
			field_alternate_title.field_alternate_title_value AS alternate_title,
			field_hero_banner.field_hero_banner_target_id AS hero_banner_target_id,
			field_icon.field_icon_target_id AS icon_target_id,
			field_metatags.field_metatags_value AS metatags,
			field_migrate_to_qq.field_migrate_to_qq_value AS migrate_to_ship_portal,
			field_agreement_type.field_agreement_type_value AS agreement_type
		FROM
			node
				LEFT JOIN node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
				LEFT JOIN `node__body` AS `body` ON node.nid = body.entity_id AND node.langcode = body.langcode
				LEFT JOIN `node__field_agreement_summary` AS `field_agreement_summary` ON node.nid = field_agreement_summary.entity_id AND node.langcode = field_agreement_summary.langcode
				LEFT JOIN `node__field_alternate_title` AS `field_alternate_title` ON node.nid = field_alternate_title.entity_id AND node.langcode = field_alternate_title.langcode
				LEFT JOIN `node__field_hero_banner` AS `field_hero_banner` ON node.nid = field_hero_banner.entity_id AND node.langcode = field_hero_banner.langcode
				LEFT JOIN `node__field_icon` AS `field_icon` ON node.nid = field_icon.entity_id AND node.langcode = field_icon.langcode
				LEFT JOIN `node__field_metatags` AS `field_metatags` ON node.nid = field_metatags.entity_id AND node.langcode = field_metatags.langcode
				LEFT JOIN `node__field_migrate_to_qq` AS `field_migrate_to_qq` ON node.nid = field_migrate_to_qq.entity_id AND node.langcode = field_migrate_to_qq.langcode
				LEFT JOIN `node__field_agreement_type` AS `field_agreement_type` ON node.nid = field_agreement_type.entity_id AND node.langcode = field_agreement_type.langcode
		WHERE
			node.type = 'agreement';";

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
