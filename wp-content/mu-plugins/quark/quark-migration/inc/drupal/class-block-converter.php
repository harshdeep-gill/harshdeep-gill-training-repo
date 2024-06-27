<?php
/**
 * Block Converter.
 *
 * @package quark-migration
 */

namespace Quark\Migration\Drupal;

use WP_CLI;
use wpdb;
use WP_Error;

/**
 * Class Block_Converter.
 */
class Block_Converter {

	/**
	 * Drupal database connection object.
	 *
	 * @var wpdb Drupal Database.
	 */
	protected wpdb $database;

	/**
	 * Constructor.
	 */
	public function __construct() {
		/**
		 * Get database connection.
		 */
		$this->database = get_database();
	}

	/**
	 * Get get_drupal_block_data block data.
	 *
	 * @param array{}|array<int|string, string|int> $block Block data.
	 *
	 * @return string
	 */
	public function get_drupal_block_data( array $block = [] ): string {
		// WordPress block.
		$wp_block = '';

		// Check if block is empty.
		if ( empty( $block ) || ! is_array( $block ) || empty( $block['id'] ) || empty( $block['type'] ) ) {
			return $wp_block;
		}

		// Switch to block type.
		switch ( $block['type'] ) {

			// Convert op_block_content block.
			case 'hero_default':
				$wp_block = $this->convert_paragraph_hero_default( $block );
				break;

			// Convert op_video block.
			case 'hero_banner':
				$wp_block = $this->convert_paragraph_hero_banner( $block );
				break;
		}

		// Return WordPress block.
		return $wp_block . PHP_EOL . PHP_EOL;
	}

	/**
	 * Convert hero_default block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_hero_default( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_hb_h1_text.field_hb_h1_text_value
		FROM
			paragraphs_item_field_data AS paragraph
			LEFT JOIN `paragraph__field_hb_h1_text` AS field_hb_h1_text ON paragraph.id = field_hb_h1_text.entity_id AND paragraph.langcode = field_hb_h1_text.langcode
		WHERE
			paragraph.type = 'hero_default' AND paragraph.id = %d AND paragraph.langcode = 'en'";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch hero_default data!' );

			// Bail out.
			return '';
		}

		// Check if section is button or not.
		$h1_text_value = ! empty( $result['field_hb_h1_text_value'] ) ? strval( $result['field_hb_h1_text_value'] ) : false;

		// Check if h1 text is available.
		if ( $h1_text_value ) {
			return serialize_block(
				[
					'blockName'    => 'quark/hero-default',
					'attrs'        => [
						'heroText' => $h1_text_value,
					],
					'innerContent' => [],
				]
			) . PHP_EOL;
		}

		// Return data.
		return '';
	}

	/**
	 * Convert hero_banner block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_hero_banner( array $block = [] ): string {
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
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch hero_banner data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs = [];

		// Check if section is button or not.
		$attrs['heroText'] = ! empty( $result['field_hb_h1_text_value'] ) ? strval( $result['field_hb_h1_text_value'] ) : '';
		$attrs['subTitle'] = ! empty( $result['field_hb_subtitle_value'] ) ? strval( $result['field_hb_subtitle_value'] ) : '';
		$image_target_id   = ! empty( $result['field_hb_image_target_id'] ) ? download_file_by_mid( absint( $result['field_hb_image_target_id'] ) ) : '';

		// Check if image found.
		if ( ! $image_target_id instanceof WP_Error ) {
			$attrs['backgroundId'] = absint( $image_target_id );
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/hero-banner',
				'attrs'        => $attrs,
				'innerContent' => [],
			]
		) . PHP_EOL;
	}
}
