<?php
/**
 * Migrate: Landing Page.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use WP_Post;
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
	 * Block converter instance.
	 *
	 * @var Block_Converter
	 */
	private Block_Converter $block_converter;

	/**
	 * Special page IDs.
	 *
	 * @var array<int>
	 */
	private $special_pages = [
		'home'                => 102236,
		'home2'               => 102696,
		'blog-archive'        => 110506,
		'website-term-of-use' => 311,
		'know-before-you-go'  => 106661,
		'expedition-ships'    => 109,
		'offers'              => 115316,
		'Brochures'           => 114791,
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

		// Initialize block converter.
		$this->block_converter = new Block_Converter();

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
		$nid            = ! empty( $item['nid'] ) ? absint( $item['nid'] ) : 0;
		$title          = '';
		$created_at     = gmdate( 'Y-m-d H:i:s' );
		$modified_at    = gmdate( 'Y-m-d H:i:s' );
		$status         = 'draft';
		$post_content   = '';
		$post_name      = '';
		$parent_post_id = 0;

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
			$post_content = serialize_block(
				[
					'blockName'    => 'quark/section',
					'attrs'        => [
						'hasTitle' => false,
						'isNarrow' => true,
					],
					'innerContent' => [ prepare_content( str_replace( '&nbsp;', ' ', strval( $item['post_content'] ) ) ) ],
				]
			) . PHP_EOL;
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
				$block_content = $this->block_converter->get_drupal_block_data( $block );
			}
		} else {
			$block_content = sprintf( '<!-- wp:quark/template-title {"title":"%s"} /-->', $title );
		}

		// Check if hero banner exists.
		if ( ! empty( $block_content ) ) {
			$post_content = $block_content . $post_content;
		}

		// Paragraphs for Post content.
		$paragraph_data = $this->get_drupal_paragraph_data( $nid );

		// Check if paragraph data is not empty.
		if ( ! empty( $paragraph_data ) ) {
			// initialize block content.
			$block_content = '';

			// Loop through paragraph data.
			foreach ( $paragraph_data as $paragraph ) {
				$block = $this->block_converter->get_drupal_block_data( $paragraph );

				// Check if block is not empty.
				if ( ! empty( $block ) ) {
					$block_content .= $block;
				}
			}

			// Append block content.
			$post_content .= $block_content;
		}

		// Prepare post content.
		$post_content = str_replace( 'u0026', '&', $post_content );

		// Post name.
		if ( ! empty( $item['drupal_url'] ) && is_string( $item['drupal_url'] ) ) {
			/**
			 * Break the url into parts and use the last part as post name.
			 * i.e. - /sea-spirit.
			 */
			$parts     = explode( '/', $item['drupal_url'] );
			$post_name = end( $parts );

			// check if $parts[1] is set.
			if ( isset( $parts[2] ) ) {
				$parent_post_name = $parts[1];

				// Get post by slug.
				$parent_post = get_page_by_path( $parent_post_name, OBJECT, 'page' );

				// Check if parent post exists.
				if ( $parent_post instanceof WP_Post ) {
					$parent_post_id = $parent_post->ID;
				}
			}
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
			'post_content'      => $post_content,
			'post_excerpt'      => wp_strip_all_tags( strval( $item['post_excerpt'] ) ),
			'post_status'       => $status,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
			'post_parent'       => $parent_post_id,
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

		// Set fallback as excerpt if meta description is empty.
		if ( empty( $data['meta_input']['_yoast_wpseo_metadesc'] ) ) {
			$data['meta_input']['_yoast_wpseo_metadesc'] = $data['post_excerpt'];
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
			( SELECT alias AS drupal_url FROM path_alias WHERE path = CONCAT( '/node/', node.nid ) ORDER BY id DESC LIMIT 0, 1 ) AS drupal_url,
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

	/**
	 * Get drupal paragraph data.
	 *
	 * @param int $nid Drupal node id.
	 *
	 * @return array{}|array<int, array<string, int|string>> Drupal paragraph data.
	 */
	public function get_drupal_paragraph_data( int $nid = 0 ): array {
		// Get database connection.
		$drupal_database = get_database();

		// Query.
		$query = "SELECT
			paragraphs_item.id,
			paragraphs_item.type
		FROM
			node__field_components as components
				LEFT JOIN paragraphs_item_field_data AS paragraphs_item ON paragraphs_item.id = components.field_components_target_id AND components.langcode = paragraphs_item.langcode AND paragraphs_item.parent_type = 'node' AND paragraphs_item.parent_id = components.entity_id
		WHERE
			components.entity_id = %s and components.langcode = 'en'
		ORDER BY delta";

		// Fetch data.
		$result = $drupal_database->get_results( $drupal_database->prepare( $query, $nid ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch paragraph data!' );

			// Bail out.
			return [];
		}

		// Return data.
		return $result;
	}
}
