<?php
/**
 * Migrate: Region Landing Pages from Drupal to WordPress CPT.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use cli\progress\Bar;
use Quark\Migration\Drupal\Block_Converter;
use WP_CLI;
use WP_Error;
use WP_CLI\ExitException;
use WP_Term;
use WP_Post;

use function Quark\Migration\Drupal\download_file_by_mid;
use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\get_term_by_id;
use function Quark\Migration\Drupal\prepare_content;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\get_post_by_id;
use function Quark\Migration\Drupal\prepare_seo_data;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\Regions\POST_TYPE;
use const Quark\Expeditions\DESTINATION_TAXONOMY;

/**
 * Class Region_Landing_Page.
 */
class Region_Landing_Page {
	/**
	 * Block_Converter instance.
	 *
	 * @var Block_Converter
	 */
	private Block_Converter $block_converter;

	/**
	 * Migrate all Region Landing Page.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts(): void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch Region Landing Pages data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for Region Landing Page!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "Region Landing Page" post-type', count( $data ) );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Create Block_Converter instance.
		$this->block_converter = new Block_Converter();

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
		WP_CLI::runcommand( 'term recount ' . DESTINATION_TAXONOMY );
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
			WP_CLI::warning( 'Unable to insert/update Region Landing Page - ' . $normalized_post['meta_input']['drupal_id'] );
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
	 *     post_excerpt: string,
	 *     post_status: string,
	 *     comment_status: string,
	 *     ping_status: string,
	 *     post_parent: int,
	 *     meta_input: array{
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
		$nid            = ! empty( $item['nid'] ) ? absint( $item['nid'] ) : 0;
		$title          = '';
		$created_at     = gmdate( 'Y-m-d H:i:s' );
		$modified_at    = gmdate( 'Y-m-d H:i:s' );
		$status         = 'draft';
		$post_content   = '';
		$post_excerpt   = '';
		$post_name      = '';
		$parent_post_id = 0;

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
				$parent_post = get_page_by_path( $parent_post_name, OBJECT, POST_TYPE );

				// Check if parent post exists.
				if ( $parent_post instanceof WP_Post ) {
					$parent_post_id = $parent_post->ID;
				}
			}
		}

		// Hero banner.
		if ( ! empty( $item['hero_banner_id'] ) ) {
			$hero_block = $this->convert_paragraph_hero_banner( [ 'id' => absint( $item['hero_banner_id'] ) ] );

			// Check if hero block is not empty.
			if ( ! empty( $hero_block ) ) {
				$post_content = $hero_block;
			}
		}

		// Paragraphs for Post content.
		$paragraph_data = $this->get_drupal_paragraph_data( $nid );

		// Check if paragraph data is not empty.
		if ( ! empty( $paragraph_data ) ) {
			foreach ( $paragraph_data as $paragraph ) {
				$block = $this->block_converter->get_drupal_block_data( $paragraph );

				// Check if block is not empty.
				if ( ! empty( $block ) ) {
					$post_content .= $block;
				}
			}
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
			'post_excerpt'      => $post_excerpt,
			'post_status'       => $status,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
			'post_parent'       => $parent_post_id,
			'meta_input'        => [
				'drupal_id' => $nid,
			],
		];

		// Set destination term.
		if ( ! empty( $item['primary_destination_id'] ) ) {
			$term = get_term_by_id( absint( $item['primary_destination_id'] ), DESTINATION_TAXONOMY );

			// Check if term exists.
			if ( $term instanceof WP_Term ) {
				$data['tax_input'][ DESTINATION_TAXONOMY ][] = $term->term_id;
			}
		}

		// SEO meta data.
		if ( ! empty( $item['metatags'] ) && is_string( $item['metatags'] ) ) {
			$seo_data = prepare_seo_data( json_decode( $item['metatags'], true ) );

			// Merge seo data if not empty.
			if ( ! empty( $seo_data ) ) {
				$data['meta_input'] = array_merge( $seo_data, $data['meta_input'] );
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
			field_hero_banner.field_hero_banner_target_id AS hero_banner_id,
			field_metatags.field_metatags_value AS metatags,
			field_primary_destination.field_primary_destination_target_id AS primary_destination_id
		FROM
			node
				LEFT JOIN node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
				LEFT JOIN node__body AS body ON node.nid = body.entity_id AND node.langcode = body.langcode
				LEFT JOIN node__field_hero_banner AS field_hero_banner ON node.nid = field_hero_banner.entity_id AND node.langcode = field_hero_banner.langcode
				LEFT JOIN node__field_metatags AS field_metatags ON node.nid = field_metatags.entity_id AND node.langcode = field_metatags.langcode
				LEFT JOIN node__field_primary_destination AS field_primary_destination ON node.nid = field_primary_destination.entity_id AND node.langcode = field_primary_destination.langcode
		WHERE
			node.type = 'region_landing_page';";

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

	/**
	 * Convert hero_banner block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_hero_banner( array $block = [] ): string {
		// Get database connection.
		$drupal_database = get_database();

		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_hb_h1_text.field_hb_h1_text_value,
			field_hb_subtitle.field_hb_subtitle_value,
			field_hb_image.field_hb_image_target_id
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_hb_h1_text AS field_hb_h1_text ON paragraph.id = field_hb_h1_text.entity_id AND paragraph.langcode = field_hb_h1_text.langcode
				LEFT JOIN paragraph__field_hb_subtitle AS field_hb_subtitle ON paragraph.id = field_hb_subtitle.entity_id AND paragraph.langcode = field_hb_subtitle.langcode
				LEFT JOIN paragraph__field_hb_image AS field_hb_image ON paragraph.id = field_hb_image.entity_id AND paragraph.langcode = field_hb_image.langcode
		WHERE
			paragraph.type = 'hero_banner' AND paragraph.id = %s AND paragraph.langcode = 'en'";

		// Fetch data.
		$result = $drupal_database->get_row( $drupal_database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch hero_banner data!' );

			// Bail out.
			return '';
		}

		// Set attributes.
		$hero_text       = ! empty( $result['field_hb_h1_text_value'] ) ? strval( $result['field_hb_h1_text_value'] ) : '';
		$sub_title       = ! empty( $result['field_hb_subtitle_value'] ) ? strval( $result['field_hb_subtitle_value'] ) : '';
		$image_target_id = ! empty( $result['field_hb_image_target_id'] ) ? download_file_by_mid( absint( $result['field_hb_image_target_id'] ) ) : '';

		// Get attachment src.
		$attachment_src = wp_get_attachment_image_src( absint( $image_target_id ), 'full' );

		// Check if attachment src found.
		if ( empty( $attachment_src ) ) {
			return '';
		}

		// Title blocks.
		$title_blocks = [
			[
				'blockName'    => 'quark/hero-title',
				'attrs'        => [
					'title'         => $hero_text,
					'syncPostTitle' => false,
				],
				'innerContent' => [],
			],
		];

		// Check if sub title is available.
		if ( ! empty( $sub_title ) ) {
			$title_blocks[] = [
				'blockName'    => 'quark/hero-subtitle',
				'attrs'        => [
					'subtitle' => $sub_title,
				],
				'innerContent' => [],
			];
		}

		// Add CTA.
		$title_blocks[] = [
			'blockName'    => 'quark/button',
			'attrs'        => [
				'btnText' => 'View Expeditions',
				'url'     => [
					'url'  => '#expeditions',
					'text' => 'View Expeditions',
				],
			],
			'innerContent' => [],
		];

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/hero',
				'attrs'        => [
					'image'             => [
						'id'     => $image_target_id,
						'src'    => $attachment_src[0],
						'width'  => $attachment_src[1],
						'height' => $attachment_src[2],
						'size'   => 'large',
					],
					'immersive'         => 'bottom',
					'contentOverlap'    => false,
					'syncPostThumbnail' => true,
				],
				'innerContent' => [
					serialize_blocks(
						[
							[
								'blockName'    => 'quark/breadcrumbs',
								'attrs'        => [],
								'innerContent' => [],
							],
							[
								'blockName'    => 'quark/hero-content',
								'attrs'        => [],
								'innerContent' => [
									serialize_block(
										[
											'blockName'    => 'quark/hero-content-left',
											'attrs'        => [],
											'innerContent' => [
												serialize_blocks( $title_blocks ) . PHP_EOL,
											],
										],
									) . PHP_EOL,
								],
							],
						],
					),
				],
			],
		) . PHP_EOL;
	}
}
