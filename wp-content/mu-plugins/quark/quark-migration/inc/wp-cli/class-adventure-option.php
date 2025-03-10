<?php
/**
 * Migrate: Adventure Options.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use cli\progress\Bar;
use WP_CLI;
use WP_Error;
use WP_Term;
use WP_CLI\ExitException;
use Quark\Migration\Drupal\Block_Converter;

use function Quark\Migration\Drupal\download_file_by_mid;
use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\get_post_by_id;
use function Quark\Migration\Drupal\get_term_by_id;
use function Quark\Migration\Drupal\prepare_content;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\AdventureOptions\POST_TYPE;
use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\Expeditions\DESTINATION_TAXONOMY;

/**
 * Class Adventure_Option.
 */
class Adventure_Option {
	/**
	 * Block_Converter instance.
	 *
	 * @var Block_Converter
	 */
	private Block_Converter $block_converter;

	/**
	 * Migrate all Adventure_Option.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts(): void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch Adventure Options data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for "Adventure Option" post-type!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "Adventure Option" post-type', count( $data ) );

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
		$post_name    = '';

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

		// Post name.
		if ( ! empty( $item['post_name'] ) && is_string( $item['post_name'] ) ) {
			/**
			 * Break the url into parts and use the last part as post name.
			 * i.e. - /adventure-options/sea-spirit.
			 */
			$parts     = explode( '/', $item['post_name'] );
			$post_name = end( $parts );
		}

		// Post excerpt.
		if ( ! empty( $item['post_excerpt'] ) && is_string( $item['post_excerpt'] ) ) {
			$post_excerpt = strval( qrk_sanitize_attribute( $item['post_excerpt'] ) );
		}

		// Init block markup.
		$hero_block_content   = '';
		$collage_block_markup = '';

		// Get hero block attributes.
		$hero_attrs = $this->get_hero_block_attrs( absint( $item['field_hero_banner_target_id'] ) );

		// Check if hero block attributes found.
		if ( ! empty( $hero_attrs ) ) {
			// Get hero block content.
			$hero_block_content = $this->prepare_hero_block_content( $hero_attrs );
		}

		// Check if images found.
		if ( ! empty( $item['images_target_ids'] ) ) {
			// Get images.
			$images = array_map( 'absint', explode( ',', strval( $item['images_target_ids'] ) ) );

			// Convert images to collage block markup.
			$collage_block_markup = PHP_EOL . $this->block_converter->convert_images_to_collage( $images );
		}

		// Prepare post content.
		$post_content = str_replace( 'u0026', '&', $hero_block_content . $post_content . $collage_block_markup );

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
			'post_name'         => $post_name,
			'post_excerpt'      => $post_excerpt,
			'post_status'       => $status,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
			'meta_input'        => [],
		];

		// Set Adventure Options taxonomy from field_adventure_options_term.
		if ( ! empty( $item['adventure_options_id'] ) ) {
			$term = get_term_by_id( absint( $item['adventure_options_id'] ), ADVENTURE_OPTION_CATEGORY );

			// Check if term exists.
			if ( $term instanceof WP_Term ) {
				$data['tax_input'][ ADVENTURE_OPTION_CATEGORY ] = $term->term_id;
			}
		}

		// Set Destination taxonomy from field_destination_ids.
		if ( ! empty( $item['field_destination_ids'] ) && is_string( $item['field_destination_ids'] ) ) {
			$destination_ids = array_map( 'absint', explode( ',', $item['field_destination_ids'] ) );

			// Set destination ids.
			foreach ( $destination_ids as $destination_id ) {
				// Get term by id.
				$term = get_term_by_id( $destination_id, DESTINATION_TAXONOMY );

				// Check if term exists.
				if ( $term instanceof WP_Term ) {
					$data['tax_input'][ DESTINATION_TAXONOMY ][] = $term->term_id;
				}
			}
		}

		// Set post thumbnail metadata.
		if ( ! empty( $hero_attrs ) && absint( $hero_attrs['hero_image'] ) ) {
			$data['meta_input']['_thumbnail_id'] = absint( $hero_attrs['hero_image'] );
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
			( SELECT alias AS drupal_url FROM path_alias WHERE path = CONCAT( '/node/', node.nid ) ORDER BY id DESC LIMIT 0, 1 ) AS post_name,
			body.body_value AS post_content,
			body.body_summary AS post_excerpt,
			field_adventure_options_term.field_adventure_options_term_target_id AS adventure_options_id,
			(SELECT GROUP_CONCAT( field_destinations_target_id ORDER BY delta SEPARATOR ', ' ) FROM node__field_destinations AS field_destinations WHERE node.nid = field_destinations.entity_id AND field_destinations.langcode = node.langcode) AS field_destination_ids,
			field_hero_banner.field_hero_banner_target_id AS `field_hero_banner_target_id`,
			(SELECT GROUP_CONCAT( field_images_target_id ORDER BY delta SEPARATOR ', ' ) FROM `node__field_images` AS `field_images` WHERE node.nid = field_images.entity_id AND field_images.langcode = node.langcode) AS images_target_ids
		FROM
			node
				LEFT JOIN node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
				LEFT JOIN `node__body` AS `body` ON node.nid = body.entity_id AND node.langcode = body.langcode
				LEFT JOIN `node__field_adventure_options_term` AS `field_adventure_options_term` ON node.nid = field_adventure_options_term.entity_id AND node.langcode = field_adventure_options_term.langcode
				LEFT JOIN `node__field_hero_banner` AS `field_hero_banner` ON node.nid = field_hero_banner.entity_id AND node.langcode = field_hero_banner.langcode
		WHERE
			node.type = 'adventure_option'";

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
	 * Get hero block attributes.
	 *
	 * @param int $hero_id Hero block ID.
	 *
	 * @return array{}|array{
	 *     hero_text : string,
	 *     hero_image : int,
	 * }
	 */
	protected function get_hero_block_attrs( int $hero_id = 0 ): array {
		// Bail out if empty.
		if ( empty( $hero_id ) ) {
			return [];
		}

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
		$result = $drupal_database->get_row( $drupal_database->prepare( $query, $hero_id ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch hero_banner data!' );

			// Bail out.
			return [];
		}

		// Set attributes.
		$hero_text       = ! empty( $result['field_hb_h1_text_value'] ) ? strval( $result['field_hb_h1_text_value'] ) : '';
		$image_target_id = ! empty( $result['field_hb_image_target_id'] ) ? download_file_by_mid( absint( $result['field_hb_image_target_id'] ) ) : '';

		// Check if image found.
		if ( $image_target_id instanceof WP_Error ) {
			return [];
		}

		// Return hero block data.
		return [
			'hero_text'  => $hero_text,
			'hero_image' => absint( $image_target_id ),
		];
	}

	/**
	 * Prepare hero block content from given ID.
	 *
	 * @param array<string, int|string> $hero_attrs Hero block attrs.
	 *
	 * @return string
	 */
	protected function prepare_hero_block_content( array $hero_attrs = [] ): string {
		// Check if hero block attributes found.
		if ( empty( $hero_attrs ) || ! absint( $hero_attrs['hero_image'] ) ) {
			return '';
		}

		// Get attachment src.
		$attachment_src = wp_get_attachment_image_src( absint( $hero_attrs['hero_image'] ), 'full' );

		// Check if attachment src found.
		if ( empty( $attachment_src ) ) {
			return '';
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/hero',
				'attrs'        => [
					'image'             => [
						'id'     => $hero_attrs['hero_image'],
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
												serialize_block(
													[
														'blockName'    => 'quark/hero-title',
														'attrs'        => [
															'title'         => $hero_attrs['hero_text'],
															'syncPostTitle' => true,
														],
														'innerContent' => [],
													]
												) . PHP_EOL,
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
