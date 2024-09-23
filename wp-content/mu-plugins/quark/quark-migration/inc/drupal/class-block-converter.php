<?php
/**
 * Block Converter.
 *
 * @package quark-migration
 */

namespace Quark\Migration\Drupal;

use WP_Post;
use WP_CLI;
use wpdb;
use WP_Error;

use const Quark\Expeditions\PrePostTripOptions\POST_TYPE as PRE_POST_TRIP_POST_TYPE;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;

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

			// Convert hero_default block.
			case 'hero_default':
				$wp_block = $this->convert_paragraph_hero_default( $block );
				break;

			// Convert hero_banner block.
			case 'hero_banner':
				$wp_block = $this->convert_paragraph_hero_banner( $block );
				break;

			// Convert cta block.
			case 'cta':
				$wp_block = $this->convert_paragraph_cta( $block );
				break;

			// Convert cta_block blocks.
			case 'cta_block':
				$wp_block = $this->convert_paragraph_cta_block( $block );
				break;

			// Convert blog_cards block.
			case 'blog_cards':
				$wp_block = $this->convert_paragraph_blog_cards( $block );
				break;

			// Convert card_group block.
			case 'card_group':
				$wp_block = $this->convert_paragraph_card_group( $block );
				break;

			// Convert expedition_cards block.
			case 'expedition_cards':
				$wp_block = $this->convert_paragraph_expedition_cards( $block );
				break;

			// Convert faq block.
			case 'faq':
				$wp_block = $this->convert_paragraph_faq( $block );
				break;

			// Convert faq_component block.
			case 'faq_component':
				$wp_block = $this->convert_paragraph_faq_component( $block );
				break;

			// Convert highlight block.
			case 'highlight':
				$wp_block = $this->convert_paragraph_highlight( $block );
				break;

			// Convert highlights block.
			case 'highlights':
				$wp_block = $this->convert_paragraph_highlights( $block );
				break;

			// Convert icon_with_text block.
			case 'icon_with_text':
				$wp_block = $this->convert_paragraph_icon_with_text( $block );
				break;

			// Convert icon_with_text_grid block.
			case 'icon_with_text_grid':
				$wp_block = $this->convert_paragraph_icon_with_text_grid( $block );
				break;

			// Convert image_gallery block.
			case 'image_gallery':
				$wp_block = $this->convert_paragraph_image_gallery( $block );
				break;

			// Convert image_with_text block.
			case 'image_with_text':
				$wp_block = $this->convert_paragraph_image_with_text( $block );
				break;

			// Convert image_with_text_list block.
			case 'image_with_text_list':
				$wp_block = $this->convert_paragraph_image_with_text_list( $block );
				break;

			// Convert numbered_cards block.
			case 'numbered_cards':
				$wp_block = $this->convert_paragraph_numbered_cards( $block );
				break;

			// Convert simple_card block.
			case 'simple_card':
				$wp_block = $this->convert_paragraph_simple_card( $block );
				break;

			// Convert offers_promotion block.
			case 'offers_promotion':
				$wp_block = $this->convert_paragraph_offers_promotion( $block );
				break;

			// Convert pre_post_trip_option block.
			case 'pre_post_trip_option':
				$wp_block = $this->convert_paragraph_pre_post_trip_option( $block );
				break;

			// Convert simple_card_group block.
			case 'simple_card_group':
				$wp_block = $this->convert_paragraph_simple_card_group( $block );
				break;

			// Convert slider block.
			case 'slider':
				$wp_block = $this->convert_paragraph_slider( $block );
				break;

			// Convert slide block.
			case 'slide':
				$wp_block = $this->convert_paragraph_slide( $block );
				break;

			// Convert staff_member_list block.
			case 'staff_member_list':
				$wp_block = $this->convert_paragraph_staff_member_list( $block );
				break;

			// Convert summary_item block.
			case 'summary_item':
				$wp_block = $this->convert_paragraph_summary_item( $block );
				break;

			// Convert summary_bar block.
			case 'summary_bar':
				$wp_block = $this->convert_paragraph_summary_bar( $block );
				break;

			// Convert testimonial block.
			case 'testimonial':
				$wp_block = $this->convert_paragraph_testimonial( $block );
				break;

			// Convert video block.
			case 'video':
				$wp_block = $this->convert_paragraph_video( $block );
				break;

			// Convert wysiwyg block.
			case 'wysiwyg':
				$wp_block = $this->convert_paragraph_wysiwyg( $block );
				break;

			// Default.
			default:
				WP_CLI::line( sprintf( 'Block type not found! - %s (Block ID - %s)', $block['type'], $block['id'] ) );
				break;
		}

		// Replace encoded ampersands.
		$wp_block = str_replace( 'u0026', '&', $wp_block );

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
					'syncPostTitle' => true,
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

	/**
	 * Convert cta block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_cta( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_cta_link.field_cta_link_uri as cta_link,
			field_cta_link.field_cta_link_title as cta_title,
			field_cta_style.field_cta_style_value as cta_style
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_cta_link AS field_cta_link ON paragraph.id = field_cta_link.entity_id AND paragraph.langcode = field_cta_link.langcode
				LEFT JOIN paragraph__field_cta_style AS field_cta_style ON paragraph.id = field_cta_style.entity_id AND paragraph.langcode = field_cta_style.langcode
		WHERE
			paragraph.type = 'cta' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch CTA paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs = [];

		// Set attributes.
		$attrs['url']['url']  = ! empty( $result['cta_link'] ) ? get_wp_permalink( strval( $result['cta_link'] ) ) : '';
		$attrs['url']['text'] = ! empty( $result['cta_title'] ) ? strval( $result['cta_title'] ) : '';
		$attrs['btnText']     = ! empty( $result['cta_title'] ) ? strval( $result['cta_title'] ) : '';

		// Check if style is primary.
		if ( 'secondary' === $result['cta_style'] ) {
			$attrs['appearance'] = 'outline';
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/button',
				'attrs'        => $attrs,
				'innerContent' => [],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert cta_block block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_cta_block( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_cta_block_title.field_cta_block_title_value as title,
			field_cta_block_description.field_cta_block_description_value as description,
			field_cta_block_cta_0.field_cta_block_cta_target_id as cta_0,
			field_cta_block_cta_1.field_cta_block_cta_target_id as cta_1
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_cta_block_title AS field_cta_block_title ON paragraph.id = field_cta_block_title.entity_id AND paragraph.langcode = field_cta_block_title.langcode
				LEFT JOIN paragraph__field_cta_block_description AS field_cta_block_description ON paragraph.id = field_cta_block_description.entity_id AND paragraph.langcode = field_cta_block_description.langcode
				LEFT JOIN paragraph__field_cta_block_cta AS field_cta_block_cta_0 ON paragraph.id = field_cta_block_cta_0.entity_id AND paragraph.langcode = field_cta_block_cta_0.langcode and field_cta_block_cta_0.delta = 0
				LEFT JOIN paragraph__field_cta_block_cta AS field_cta_block_cta_1 ON paragraph.id = field_cta_block_cta_1.entity_id AND paragraph.langcode = field_cta_block_cta_1.langcode and field_cta_block_cta_1.delta = 1
		WHERE
			paragraph.type = 'cta_block' AND paragraph.id = %s AND paragraph.langcode = 'en'";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch cta_block paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$block_markup = '';

		// if title set create block H4.
		if ( ! empty( $result['title'] ) ) {
			$block_markup .= sprintf( '<!-- wp:heading {"level":2} --><h2 class="wp-block-heading">%s</h2><!-- /wp:heading -->', strval( $result['title'] ) ) . PHP_EOL;
		}

		// if description set create blocks.
		if ( ! empty( $result['description'] ) ) {
			$block_markup .= prepare_content( strval( $result['description'] ) ) . PHP_EOL;
		}

		// Buttons Markup.
		$buttons_markup = '';

		// Check if cta_0 is available.
		if ( ! empty( $result['cta_0'] ) ) {
			$cta_0 = $this->convert_paragraph_cta( [ 'id' => $result['cta_0'] ] );

			// Check if cta_0 is available.
			if ( ! empty( $cta_0 ) ) {
				$buttons_markup .= $cta_0;
			}
		}

		// Check if cta_1 is available.
		if ( ! empty( $result['cta_1'] ) ) {
			$cta_1 = $this->convert_paragraph_cta( [ 'id' => $result['cta_1'] ] );

			// Check if cta_1 is available.
			if ( ! empty( $cta_1 ) ) {
				$buttons_markup .= $cta_1;
			}
		}

		// Check if buttons markup is available.
		if ( ! empty( $buttons_markup ) ) {
			// Serialize Block - quark/buttons.
			$block_markup .= serialize_block(
				[
					'blockName'    => 'quark/buttons',
					'attrs'        => [],
					'innerContent' => [ $buttons_markup ],
				]
			) . PHP_EOL;
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/cta-banner',
				'attrs'        => [],
				'innerContent' => [ $block_markup ],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert blog_cards block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_blog_cards( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_component_background.field_component_background_value as background,
			field_blog_cards_title.field_blog_cards_title_value as title,
			(SELECT GROUP_CONCAT( field_blog_cards_target_id ORDER BY delta SEPARATOR ', ' ) FROM paragraph__field_blog_cards AS field_blog_cards WHERE paragraph.id = field_blog_cards.entity_id AND field_blog_cards.langcode = paragraph.langcode) AS blog_cards
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_component_background AS field_component_background ON paragraph.id = field_component_background.entity_id AND paragraph.langcode = field_component_background.langcode
				LEFT JOIN paragraph__field_blog_cards_title AS field_blog_cards_title ON paragraph.id = field_blog_cards_title.entity_id AND paragraph.langcode = field_blog_cards_title.langcode

		WHERE
			paragraph.type = 'blog_cards' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch blog_cards paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs  = [];
		$markup = '';

		// Check if cards are available.
		if ( ! empty( $result['blog_cards'] ) ) {
			$cards    = array_map( 'absint', explode( ',', $result['blog_cards'] ) );
			$card_ids = [];

			// Loop through each card.
			foreach ( $cards as $card ) {
				$post = get_post_by_id( $card );

				// validate post.
				if ( $post instanceof WP_Post ) {
					$card_ids[] = $post->ID;
				}
			}

			// Check if card ids are available.
			if ( ! empty( $card_ids ) ) {
				// Serialize Block - quark/expeditions.
				$markup = serialize_block(
					[
						'blockName'    => 'quark/blog-post-cards',
						'attrs'        => [
							'ids' => $card_ids,
						],
						'innerContent' => [],
					]
				) . PHP_EOL;
			}
		}

		// if title is not available then return.
		if ( empty( $result['title'] ) ) {
			return $markup;
		}

		// Set attributes.
		$attrs['title']          = strval( $result['title'] );
		$attrs['anchor']         = sanitize_title_with_dashes( $result['title'] );
		$attrs['titleAlignment'] = 'left';
		$attrs['headingLevel']   = 'h2';

		// Access secondary nav.
		global $secondary_nav;

		// Add to secondary nav.
		$secondary_nav[] = [
			'title' => $attrs['title'],
			'url'   => get_wp_permalink( $attrs['anchor'] ),
		];

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/section',
				'attrs'        => $attrs,
				'innerContent' => [ $markup ],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert card_group block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_card_group( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_component_background.field_component_background_value as background,
			field_card_group_title.field_card_group_title_value as title,
			(SELECT GROUP_CONCAT( field_card_group_entities_target_id ORDER BY delta SEPARATOR ', ' ) FROM paragraph__field_card_group_entities AS field_card_group_entities WHERE paragraph.id = field_card_group_entities.entity_id AND field_card_group_entities.langcode = paragraph.langcode) AS cards
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_component_background AS field_component_background ON paragraph.id = field_component_background.entity_id AND paragraph.langcode = field_component_background.langcode
				LEFT JOIN paragraph__field_card_group_title AS field_card_group_title ON paragraph.id = field_card_group_title.entity_id AND paragraph.langcode = field_card_group_title.langcode

		WHERE
			paragraph.type = 'card_group' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch card_group paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs = [
			'cards' => [],
		];

		// Set attributes.
		$attrs['background'] = ! empty( $result['background'] ) ? strval( $result['background'] ) : '';
		$attrs['title']      = ! empty( $result['title'] ) ? strval( $result['title'] ) : '';

		// Check if cards are available.
		if ( ! empty( $result['cards'] ) ) {
			$cards = explode( ',', $result['cards'] );

			// Loop through each blog card.
			foreach ( $cards as $card ) {
				$attrs['cards'][] = get_post_by_id( absint( $card ), [ EXPEDITION_POST_TYPE, SHIP_POST_TYPE ] );
			}
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/related-cards',
				'attrs'        => $attrs,
				'innerContent' => [],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert expedition_cards block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_expedition_cards( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_component_background.field_component_background_value as background,
			field_card_group_title.field_card_group_title_value as title,
			(SELECT GROUP_CONCAT( field_card_group_entities_target_id ORDER BY delta SEPARATOR ', ' ) FROM paragraph__field_card_group_entities AS field_card_group_entities WHERE paragraph.id = field_card_group_entities.entity_id AND field_card_group_entities.langcode = paragraph.langcode) AS cards
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_component_background AS field_component_background ON paragraph.id = field_component_background.entity_id AND paragraph.langcode = field_component_background.langcode
				LEFT JOIN paragraph__field_card_group_title AS field_card_group_title ON paragraph.id = field_card_group_title.entity_id AND paragraph.langcode = field_card_group_title.langcode

		WHERE
			paragraph.type = 'expedition_cards' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch expedition_cards paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs  = [];
		$markup = '';

		// Check if cards are available.
		if ( ! empty( $result['cards'] ) ) {
			$cards    = array_map( 'absint', explode( ',', $result['cards'] ) );
			$card_ids = [];

			// Loop through each card.
			foreach ( $cards as $card ) {
				$expedition_post = get_post_by_id( $card, EXPEDITION_POST_TYPE );

				// validate post.
				if ( $expedition_post instanceof WP_Post ) {
					$card_ids[] = $expedition_post->ID;
				}
			}

			// Check if card ids are available.
			if ( ! empty( $card_ids ) ) {
				// Serialize Block - quark/expeditions.
				$markup = serialize_block(
					[
						'blockName'    => 'quark/expeditions',
						'attrs'        => [
							'ids' => $card_ids,
						],
						'innerContent' => [],
					]
				) . PHP_EOL;
			}
		}

		// if title is not available then return.
		if ( empty( $result['title'] ) ) {
			return $markup;
		}

		// Set attributes.
		$attrs['title']          = strval( $result['title'] );
		$attrs['anchor']         = 'expeditions';
		$attrs['titleAlignment'] = 'left';
		$attrs['headingLevel']   = 'h2';

		// Access secondary nav.
		global $secondary_nav;

		// Add to secondary nav.
		$secondary_nav[] = [
			'title' => $attrs['title'],
			'url'   => get_wp_permalink( $attrs['anchor'] ),
		];

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/section',
				'attrs'        => $attrs,
				'innerContent' => [ $markup ],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert faq block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_faq( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_faq_question.field_faq_question_value as question,
			field_faq_answer.field_faq_answer_value as answer
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_faq_question AS field_faq_question ON paragraph.id = field_faq_question.entity_id AND paragraph.langcode = field_faq_question.langcode
				LEFT JOIN paragraph__field_faq_answer AS field_faq_answer ON paragraph.id = field_faq_answer.entity_id AND paragraph.langcode = field_faq_answer.langcode
		WHERE
			paragraph.type = 'faq' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch faq paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs = [];

		// Set attributes.
		$attrs['question'] = ! empty( $result['question'] ) ? strval( $result['question'] ) : '';
		$answer            = ! empty( $result['answer'] ) ? prepare_content( strval( $result['answer'] ) ) : '';

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/faq',
				'attrs'        => $attrs,
				'innerContent' => [],
				'innerHTML'    => $answer,
			]
		) . PHP_EOL;
	}

	/**
	 * Convert faq_component block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_faq_component( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_component_background.field_component_background_value as background,
			field_faq_component_title.field_faq_component_title_value as title,
			(SELECT GROUP_CONCAT( field_faqs_target_id ORDER BY delta SEPARATOR ', ' ) FROM paragraph__field_faqs AS field_faqs WHERE paragraph.id = field_faqs.entity_id AND field_faqs.langcode = paragraph.langcode) AS faqs
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_component_background AS field_component_background ON paragraph.id = field_component_background.entity_id AND paragraph.langcode = field_component_background.langcode
				LEFT JOIN paragraph__field_faq_component_title AS field_faq_component_title ON paragraph.id = field_faq_component_title.entity_id AND paragraph.langcode = field_faq_component_title.langcode
		WHERE
			paragraph.type = 'faq_component' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch faq_component paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs      = [];
		$faq_blocks = '';

		// Set attributes.
		$attrs['background'] = ! empty( $result['background'] ) ? strval( $result['background'] ) : '';
		$attrs['title']      = ! empty( $result['title'] ) ? strval( $result['title'] ) : '';

		// Check if cards are available.
		if ( ! empty( $result['faqs'] ) ) {
			$faqs = explode( ',', $result['faqs'] );

			// Loop through each faq.
			foreach ( $faqs as $faq ) {
				$faq_blocks .= $this->convert_paragraph_faq( [ 'id' => $faq ] );
			}
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/faq-component',
				'attrs'        => $attrs,
				'innerContent' => [],
				'innerHTML'    => $faq_blocks,
			]
		) . PHP_EOL;
	}

	/**
	 * Convert highlight block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_highlight( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_highlight.field_highlight_value as highlight,
			(SELECT taxonomy_term__field_svg_file.field_svg_file_target_id FROM taxonomy_term__field_svg_file WHERE field_highlight_icon.field_highlight_icon_target_id = taxonomy_term__field_svg_file.entity_id) as icon_id
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_highlight AS field_highlight ON paragraph.id = field_highlight.entity_id AND paragraph.langcode = field_highlight.langcode
				LEFT JOIN paragraph__field_highlight_icon AS field_highlight_icon ON paragraph.id = field_highlight_icon.entity_id AND paragraph.langcode = field_highlight_icon.langcode
		WHERE
			paragraph.type = 'highlight' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch highlight paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs = [];

		// Set attributes.
		$attrs['title']  = ! empty( $result['highlight'] ) ? strval( $result['highlight'] ) : '';
		$image_target_id = ! empty( $result['image'] ) ? download_file_by_fid( absint( $result['image'] ) ) : '';

		// Check if image found.
		if ( ! $image_target_id instanceof WP_Error ) {
			$attrs['image'] = absint( $image_target_id );
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/highlight',
				'attrs'        => $attrs,
				'innerContent' => [],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert highlights block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_highlights( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_image.field_image_target_id as image,
			(SELECT GROUP_CONCAT( field_highlights_target_id ORDER BY delta SEPARATOR ', ' ) FROM paragraph__field_highlights AS field_highlights WHERE paragraph.id = field_highlights.entity_id AND field_highlights.langcode = paragraph.langcode) AS highlights
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_image AS field_image ON paragraph.id = field_image.entity_id AND paragraph.langcode = field_image.langcode
		WHERE
			paragraph.type = 'highlights' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch highlights paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs      = [];
		$highlights = '';

		// Set attributes.
		$image_target_id = ! empty( $result['image'] ) ? download_file_by_mid( absint( $result['image'] ) ) : '';

		// Check if image found.
		if ( ! $image_target_id instanceof WP_Error ) {
			$attrs['image'] = absint( $image_target_id );
		}

		// Check if highlights are available.
		if ( ! empty( $result['highlights'] ) ) {
			$highlights = explode( ',', $result['highlights'] );

			// Loop through each card.
			foreach ( $highlights as $highlight ) {
				$highlights .= $this->convert_paragraph_highlight( [ 'id' => $highlight ] );
			}
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/highlights',
				'attrs'        => $attrs,
				'innerContent' => [],
				'innerHTML'    => sprintf( '<div class="quark-highlights--wrapper">%s</div>', $highlights ),
			]
		) . PHP_EOL;
	}

	/**
	 * Convert icon_with_text block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_icon_with_text( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_icon_with_text_title.field_icon_with_text_title_value as title,
			field_icon_with_text_description.field_icon_with_text_description_value as description,
			(SELECT taxonomy_term__field_svg_file.field_svg_file_target_id FROM taxonomy_term__field_svg_file WHERE field_icon_with_text_icon.field_icon_with_text_icon_target_id = taxonomy_term__field_svg_file.entity_id) as icon_id
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_icon_with_text_title AS field_icon_with_text_title ON paragraph.id = field_icon_with_text_title.entity_id AND paragraph.langcode = field_icon_with_text_title.langcode
				LEFT JOIN paragraph__field_icon_with_text_description AS field_icon_with_text_description ON paragraph.id = field_icon_with_text_description.entity_id AND paragraph.langcode = field_icon_with_text_description.langcode
				LEFT JOIN paragraph__field_icon_with_text_icon AS field_icon_with_text_icon ON paragraph.id = field_icon_with_text_icon.entity_id AND paragraph.langcode = field_icon_with_text_icon.langcode
		WHERE
			paragraph.type = 'icon_with_text' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch icon_with_text paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$block_markup = '';

		// if title set create block H4.
		if ( ! empty( $result['title'] ) ) {
			$block_markup .= sprintf( '<!-- wp:heading {"level":4} --><h4 class="wp-block-heading">%s</h4><!-- /wp:heading -->', $result['title'] ) . PHP_EOL;
		}

		// if description set create block paragraph.
		if ( ! empty( $result['description'] ) ) {
			$block_markup .= prepare_content( strval( $result['description'] ) ) . PHP_EOL;
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/icon-info-grid-item',
				'attrs'        => [],
				'innerContent' => [ $block_markup ],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert icon_with_text_grid block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_icon_with_text_grid( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_icon_with_text_grid_title.field_icon_with_text_grid_title_value as title,
			field_component_background.field_component_background_value as background,
			(SELECT GROUP_CONCAT( field_icon_with_text_blocks_target_id ORDER BY delta SEPARATOR ', ' ) FROM paragraph__field_icon_with_text_blocks AS field_icon_with_text_blocks WHERE paragraph.id = field_icon_with_text_blocks.entity_id AND field_icon_with_text_blocks.langcode = paragraph.langcode) AS icon_with_text_blocks
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_icon_with_text_grid_title AS field_icon_with_text_grid_title ON paragraph.id = field_icon_with_text_grid_title.entity_id AND paragraph.langcode = field_icon_with_text_grid_title.langcode
				LEFT JOIN paragraph__field_component_background AS field_component_background ON paragraph.id = field_component_background.entity_id AND paragraph.langcode = field_component_background.langcode
		WHERE
			paragraph.type = 'icon_with_text_grid' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch icon_with_text_grid paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs                       = [];
		$icon_with_text_block_markup = '';

		// Set attributes.
		$attrs['title']          = ! empty( $result['title'] ) ? strval( $result['title'] ) : '';
		$attrs['anchor']         = sanitize_title_with_dashes( $result['title'] );
		$attrs['titleAlignment'] = 'left';
		$attrs['headingLevel']   = 'h2';

		// Check if icon_with_text_blocks are available.
		if ( ! empty( $result['icon_with_text_blocks'] ) ) {
			$icon_with_text_blocks = array_map( 'absint', explode( ',', $result['icon_with_text_blocks'] ) );

			// Loop through each block.
			foreach ( $icon_with_text_blocks as $icon_with_text_block ) {
				$icon_with_text_block_markup .= $this->convert_paragraph_icon_with_text( [ 'id' => $icon_with_text_block ] );
			}

			// Serialize Block - quark/icon-info-grid.
			$icon_with_text_block_markup = serialize_block(
				[
					'blockName'    => 'quark/icon-info-grid',
					'attrs'        => [],
					'innerContent' => [ $icon_with_text_block_markup ],
				]
			) . PHP_EOL;
		}

		// Check title is set.
		if ( ! empty( $attrs['title'] ) ) {
			global $secondary_nav;

			// Add to secondary nav.
			$secondary_nav[] = [
				'title' => $attrs['title'],
				'url'   => get_wp_permalink( $attrs['anchor'] ),
			];
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/section',
				'attrs'        => $attrs,
				'innerContent' => [ $icon_with_text_block_markup ],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert image_with_text block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 * @param string                                $align Image alignment.
	 *
	 * @return string
	 */
	public function convert_paragraph_image_with_text( array $block = [], string $align = 'right' ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_image_text_title.field_image_text_title_value as title,
			field_image_text_subtitle.field_image_text_subtitle_value as subtitle,
			field_image_text_description.field_image_text_description_value as description,
			field_component_background.field_component_background_value as background,
			field_image_text_link.field_image_text_link_uri as link,
			field_image_text_link.field_image_text_link_title as link_title,
			field_image_text_secondary_link.field_image_text_secondary_link_uri as secondary_link,
			field_image_text_secondary_link.field_image_text_secondary_link_title as secondary_link_title,
			field_image.field_image_target_id as media
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_image_text_title AS field_image_text_title ON paragraph.id = field_image_text_title.entity_id AND paragraph.langcode = field_image_text_title.langcode
				LEFT JOIN paragraph__field_image_text_subtitle AS field_image_text_subtitle ON paragraph.id = field_image_text_subtitle.entity_id AND paragraph.langcode = field_image_text_subtitle.langcode
				LEFT JOIN paragraph__field_image_text_description AS field_image_text_description ON paragraph.id = field_image_text_description.entity_id AND paragraph.langcode = field_image_text_description.langcode
				LEFT JOIN paragraph__field_component_background AS field_component_background ON paragraph.id = field_component_background.entity_id AND paragraph.langcode = field_component_background.langcode
				LEFT JOIN paragraph__field_image_text_link AS field_image_text_link ON paragraph.id = field_image_text_link.entity_id AND paragraph.langcode = field_image_text_link.langcode
				LEFT JOIN paragraph__field_image_text_secondary_link AS field_image_text_secondary_link ON paragraph.id = field_image_text_secondary_link.entity_id AND paragraph.langcode = field_image_text_secondary_link.langcode
				LEFT JOIN paragraph__field_image AS field_image ON paragraph.id = field_image.entity_id AND paragraph.langcode = field_image.langcode
		WHERE
			paragraph.type = 'image_with_text' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch image_with_text paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$block_markup = '';

		// if title set create block H3.
		if ( ! empty( $result['title'] ) ) {
			$block_markup .= sprintf( '<!-- wp:heading {"level":3} --><h3>%s</h3><!-- /wp:heading -->', $result['title'] ) . PHP_EOL;
		}

		// if subtitle set create block H4.
		if ( ! empty( $result['subtitle'] ) ) {
			$block_markup .= sprintf( '<!-- wp:heading {"level":4} --><h4>%s</h4><!-- /wp:heading -->', $result['subtitle'] ) . PHP_EOL;
		}

		// if description set create block paragraph.
		if ( ! empty( $result['description'] ) ) {
			$block_markup .= prepare_content( strval( $result['description'] ) ) . PHP_EOL;
		}

		// Buttons Markup.
		$buttons = '';

		// Check if link is available.
		if ( ! empty( $result['link'] ) ) {
			// Get link text.
			$link_text = ! empty( $result['link_title'] ) ? strval( $result['link_title'] ) : '';

			// Create button block.
			$buttons .= serialize_block(
				[
					'blockName'    => 'quark/button',
					'attrs'        => [
						'url'     => [
							'url'  => get_wp_permalink( strval( $result['link'] ) ),
							'text' => $link_text,
						],
						'btnText' => $link_text,
					],
					'innerContent' => [],
				]
			) . PHP_EOL;
		}

		// Check if secondary link is available.
		if ( ! empty( $result['secondary_link'] ) ) {
			// Get link text.
			$secondary_link_text = ! empty( $result['secondary_link_title'] ) ? strval( $result['secondary_link_title'] ) : '';

			// Create button block.
			$buttons .= serialize_block(
				[
					'blockName'    => 'quark/button',
					'attrs'        => [
						'backgroundColor' => 'black',
						'url'             => [
							'url'  => get_wp_permalink( strval( $result['secondary_link'] ) ),
							'text' => $secondary_link_text,
						],
						'btnText'         => $secondary_link_text,
					],
					'innerContent' => [],
				]
			) . PHP_EOL;
		}

		// If buttons are available wrap in media-text-cta-cta block.
		if ( ! empty( $buttons ) ) {
			$block_markup .= serialize_block(
				[
					'blockName'    => 'quark/media-text-cta-cta',
					'attrs'        => [],
					'innerContent' => [ $buttons ],
				]
			) . PHP_EOL;
		}

		// Block attrs.
		$attrs = [];

		// Check if media is available.
		if ( ! empty( $result['media'] ) ) {
			$media_target_id = download_file_by_mid( absint( $result['media'] ) );

			// Check if image found.
			if ( ! $media_target_id instanceof WP_Error ) {
				$attachment_src = wp_get_attachment_image_src( absint( $media_target_id ), 'full' );

				// Check if attachment src found.
				if ( ! empty( $attachment_src ) ) {
					$attrs['image'] = [
						'id'     => $media_target_id,
						'src'    => $attachment_src[0],
						'width'  => $attachment_src[1],
						'height' => $attachment_src[2],
						'size'   => 'large',
					];
				}
			}
		}

		// Align media to right.
		if ( 'right' === $align ) {
			$attrs['mediaAlignment'] = 'right';
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/media-text-cta',
				'attrs'        => $attrs,
				'innerContent' => [ $block_markup ],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert image_gallery block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_image_gallery( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_gallery_title.field_gallery_title_value as title,
			field_gallery_description.field_gallery_description_value as description,
			field_gallery_style.field_gallery_style_value as background,
			field_gallery_highlight.field_gallery_highlight_target_id as highlight_id,
			(SELECT GROUP_CONCAT( field_gallery_image_target_id ORDER BY delta SEPARATOR ', ' ) FROM paragraph__field_gallery_image AS field_gallery_image WHERE paragraph.id = field_gallery_image.entity_id AND field_gallery_image.langcode = paragraph.langcode) AS gallery_images
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_gallery_title AS field_gallery_title ON paragraph.id = field_gallery_title.entity_id AND paragraph.langcode = field_gallery_title.langcode
				LEFT JOIN paragraph__field_gallery_description AS field_gallery_description ON paragraph.id = field_gallery_description.entity_id AND paragraph.langcode = field_gallery_description.langcode
				LEFT JOIN paragraph__field_gallery_style AS field_gallery_style ON paragraph.id = field_gallery_style.entity_id AND paragraph.langcode = field_gallery_style.langcode
				LEFT JOIN paragraph__field_gallery_highlight AS field_gallery_highlight ON paragraph.id = field_gallery_highlight.entity_id AND paragraph.langcode = field_gallery_highlight.langcode
		WHERE
			paragraph.type = 'image_gallery' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if image_gallery data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch image_gallery paragraph data!' );

			// Bail out.
			return '';
		}

		// image_gallery Block Markup.
		$attrs = [];

		// Set attributes.
		$collage_block_markup    = '';
		$attrs['background']     = ! empty( $result['background'] ) ? strval( $result['background'] ) : '';
		$attrs['title']          = ! empty( $result['title'] ) ? strval( $result['title'] ) : '';
		$attrs['anchor']         = sanitize_title_with_dashes( $result['title'] );
		$attrs['titleAlignment'] = 'left';
		$attrs['headingLevel']   = 'h2';

		// Set description.
		if ( ! empty( $result['description'] ) ) {
			$attrs['description']    = wp_strip_all_tags( $result['description'] );
			$attrs['hasDescription'] = true;
		}

		// Check if media is available.
		if ( ! empty( $result['gallery_images'] ) ) {
			// Get images.
			$images = array_map( 'absint', explode( ',', strval( $result['gallery_images'] ) ) );

			// Convert images to collage block markup.
			$collage_block_markup = $this->convert_images_to_collage( $images );
		}

		// If title is set wrap collage in section.
		if ( empty( $attrs['title'] ) ) {
			return $collage_block_markup;
		} else {
			global $secondary_nav;

			// Add to secondary nav.
			$secondary_nav[] = [
				'title' => $attrs['title'],
				'url'   => get_wp_permalink( $attrs['anchor'] ),
			];
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/section',
				'attrs'        => $attrs,
				'innerContent' => [ $collage_block_markup ],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert image_with_text_list block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_image_with_text_list( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_itc_title.field_itc_title_value as title,
			field_component_background.field_component_background_value as background,
			(SELECT GROUP_CONCAT( field_itc_image_text_target_id ORDER BY delta SEPARATOR ', ' ) FROM paragraph__field_itc_image_text AS field_itc_image_text WHERE paragraph.id = field_itc_image_text.entity_id AND field_itc_image_text.langcode = paragraph.langcode) AS itc_image_text
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_itc_title AS field_itc_title ON paragraph.id = field_itc_title.entity_id AND paragraph.langcode = field_itc_title.langcode
				LEFT JOIN paragraph__field_component_background AS field_component_background ON paragraph.id = field_component_background.entity_id AND paragraph.langcode = field_component_background.langcode
		WHERE
			paragraph.type = 'image_with_text_list' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch image_with_text_list paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs                         = [];
		$image_with_text_block_content = '';

		// Set attributes.
		$attrs['background']     = ! empty( $result['background'] ) ? strval( $result['background'] ) : '';
		$attrs['title']          = ! empty( $result['title'] ) ? strval( $result['title'] ) : '';
		$attrs['anchor']         = sanitize_title_with_dashes( $result['title'] );
		$attrs['titleAlignment'] = 'left';
		$attrs['headingLevel']   = 'h2';

		// Check if icon_with_text_blocks are available.
		if ( ! empty( $result['itc_image_text'] ) ) {
			$itc_image_text_blocks = array_map( 'absint', explode( ',', $result['itc_image_text'] ) );

			// Loop through each block.
			foreach ( $itc_image_text_blocks as $index => $itc_image_text_block ) {
				// set align as left for even blocks and right for odd blocks.
				$align = 0 === $index % 2 ? 'right' : 'left';

				// Convert image_with_text block.
				$image_with_text_block_content .= $this->convert_paragraph_image_with_text( [ 'id' => $itc_image_text_block ], $align );
			}
		}

		// Check if title is set.
		if ( ! empty( $attrs['title'] ) ) {
			global $secondary_nav;

			// Add to secondary nav.
			$secondary_nav[] = [
				'title' => $attrs['title'],
				'url'   => get_wp_permalink( $attrs['anchor'] ),
			];
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/section',
				'attrs'        => $attrs,
				'innerContent' => [ $image_with_text_block_content ],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert numbered_cards block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_numbered_cards( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_numbered_card_title.field_numbered_card_title_value as title,
			field_component_background.field_component_background_value as background,
			(SELECT GROUP_CONCAT( field_numbered_card_target_id ORDER BY delta SEPARATOR ', ' ) FROM paragraph__field_numbered_card AS field_numbered_card WHERE paragraph.id = field_numbered_card.entity_id AND field_numbered_card.langcode = paragraph.langcode) AS numbered_cards
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_numbered_card_title AS field_numbered_card_title ON paragraph.id = field_numbered_card_title.entity_id AND paragraph.langcode = field_numbered_card_title.langcode
				LEFT JOIN paragraph__field_component_background AS field_component_background ON paragraph.id = field_component_background.entity_id AND paragraph.langcode = field_component_background.langcode
		WHERE
			paragraph.type = 'numbered_cards' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch numbered_cards paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs                 = [];
		$numbered_card_content = '';

		// Set attributes.
		$attrs['background']     = ! empty( $result['background'] ) ? strval( $result['background'] ) : '';
		$attrs['title']          = ! empty( $result['title'] ) ? strval( $result['title'] ) : '';
		$attrs['anchor']         = sanitize_title_with_dashes( $result['title'] );
		$attrs['titleAlignment'] = 'left';
		$attrs['headingLevel']   = 'h2';

		// Check if numbered_cards are available.
		if ( ! empty( $result['numbered_cards'] ) ) {
			$numbered_card_blocks = array_map( 'absint', explode( ',', $result['numbered_cards'] ) );

			// Loop through each block.
			foreach ( $numbered_card_blocks as $numbered_card_block ) {
				$numbered_card_content .= $this->convert_paragraph_simple_card( [ 'id' => $numbered_card_block ] );
			}
		}

		// Check if title is set.
		if ( ! empty( $attrs['title'] ) ) {
			global $secondary_nav;

			// Add to secondary nav.
			$secondary_nav[] = [
				'title' => $attrs['title'],
				'url'   => get_wp_permalink( $attrs['anchor'] ),
			];
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/section',
				'attrs'        => $attrs,
				'innerContent' => [
					serialize_block(
						[
							'blockName'    => 'quark/media-description-cards',
							'attrs'        => [],
							'innerContent' => [ $numbered_card_content ],
						]
					),
				],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert simple_card block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_simple_card( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_simple_card_title.field_simple_card_title_value as title,
			field_simple_card_text.field_simple_card_text_value as description,
			field_simple_card_image.field_simple_card_image_target_id as image
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_simple_card_title AS field_simple_card_title ON paragraph.id = field_simple_card_title.entity_id AND paragraph.langcode = field_simple_card_title.langcode
				LEFT JOIN paragraph__field_simple_card_text AS field_simple_card_text ON paragraph.id = field_simple_card_text.entity_id AND paragraph.langcode = field_simple_card_text.langcode
				LEFT JOIN paragraph__field_simple_card_image AS field_simple_card_image ON paragraph.id = field_simple_card_image.entity_id AND paragraph.langcode = field_simple_card_image.langcode
		WHERE
			paragraph.type = 'simple_card' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch simple_card paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs = [];

		// Set attributes.
		$attrs['title']       = ! empty( $result['title'] ) ? strval( $result['title'] ) : '';
		$attrs['description'] = ! empty( $result['description'] ) ? wp_strip_all_tags( strval( $result['description'] ) ) : '';

		// Check if image is available.
		if ( ! empty( $result['image'] ) ) {
			$image_target_id = download_file_by_mid( absint( $result['image'] ) );

			// Check if image found.
			if ( ! $image_target_id instanceof WP_Error ) {
				// Get attachment src.
				$attachment_src = wp_get_attachment_image_src( absint( $image_target_id ), 'full' );

				// Check if attachment src found.
				if ( empty( $attachment_src ) ) {
					return '';
				}

				// Set image attributes.
				$attrs['image'] = [
					'id'     => $image_target_id,
					'src'    => $attachment_src[0],
					'width'  => $attachment_src[1],
					'height' => $attachment_src[2],
					'size'   => 'large',
				];
			}
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/media-description-card',
				'attrs'        => $attrs,
				'innerContent' => [],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert offers_promotion block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_offers_promotion( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_offers_title.field_offers_title_value as title,
			field_filters.field_filters_value as filters,
			field_departure_date_span.field_departure_date_span_value as departure_date_span,
			field_display_filters.field_display_filters_value as show_filters,
			field_primary_offer_cta.field_primary_offer_cta_value as primary_offer_cta,
			field_secondary_offer_cta.field_secondary_offer_cta_value as secondary_offer_cta
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_offers_title AS field_offers_title ON paragraph.id = field_offers_title.entity_id AND paragraph.langcode = field_offers_title.langcode
				LEFT JOIN paragraph__field_filters AS field_filters ON paragraph.id = field_filters.entity_id AND paragraph.langcode = field_filters.langcode
				LEFT JOIN paragraph__field_departure_date_span AS field_departure_date_span ON paragraph.id = field_departure_date_span.entity_id AND paragraph.langcode = field_departure_date_span.langcode
				LEFT JOIN paragraph__field_display_filters AS field_display_filters ON paragraph.id = field_display_filters.entity_id AND paragraph.langcode = field_display_filters.langcode
				LEFT JOIN paragraph__field_primary_offer_cta AS field_primary_offer_cta ON paragraph.id = field_primary_offer_cta.entity_id AND paragraph.langcode = field_primary_offer_cta.langcode
				LEFT JOIN paragraph__field_secondary_offer_cta AS field_secondary_offer_cta ON paragraph.id = field_secondary_offer_cta.entity_id AND paragraph.langcode = field_secondary_offer_cta.langcode
		WHERE
			paragraph.type = 'offers_promotion' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch offers_promotion paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs = [];

		// Set attributes.
		$attrs['title']        = ! empty( $result['title'] ) ? strval( $result['title'] ) : '';
		$attrs['dateSpan']     = ! empty( $result['departure_date_span'] ) ? absint( $result['departure_date_span'] ) : '';
		$attrs['showFilters']  = boolval( $result['show_filters'] );
		$attrs['primaryCta']   = ! empty( $result['primary_offer_cta'] ) ? strval( $result['primary_offer_cta'] ) : '';
		$attrs['secondaryCta'] = ! empty( $result['secondary_offer_cta'] ) ? strval( $result['secondary_offer_cta'] ) : '';
		$filters               = ! empty( $result['filters'] ) ? urldecode( $result['filters'] ) : '';

		// Parse filters.
		parse_str( $filters, $params );
		$attrs['filters'] = $params;

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/offers-promotion',
				'attrs'        => $attrs,
				'innerContent' => [],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert pre_post_trip_option block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_pre_post_trip_option( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_component_background.field_component_background_value as background,
			field_options_lead_in.field_options_lead_in_value as description,
			( SELECT GROUP_CONCAT( field_options_items_target_id ORDER BY delta SEPARATOR ', ' ) FROM paragraph__field_options_items AS field_options_items WHERE paragraph.id = field_options_items.entity_id AND field_options_items.langcode = paragraph.langcode ) AS options_items
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_component_background AS field_component_background ON paragraph.id = field_component_background.entity_id AND paragraph.langcode = field_component_background.langcode
				LEFT JOIN paragraph__field_options_lead_in AS field_options_lead_in ON paragraph.id = field_options_lead_in.entity_id AND paragraph.langcode = field_options_lead_in.langcode
		WHERE
			paragraph.type = 'pre_post_trip_option' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch pre_post_trip_option paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs = [];

		// Set attributes.
		$attrs['background']  = ! empty( $result['background'] ) ? strval( $result['background'] ) : '';
		$attrs['description'] = ! empty( $result['description'] ) ? strval( $result['description'] ) : '';

		// Check if options_items are available.
		if ( ! empty( $result['options_items'] ) ) {
			$options_items = explode( ',', $result['options_items'] );

			// Loop through each block.
			foreach ( $options_items as $options_item ) {
				$post = get_post_by_id( absint( $options_item ), PRE_POST_TRIP_POST_TYPE );

				// Check if post found.
				if ( $post instanceof WP_Post ) {
					$attrs['posts'][] = $post->post_name;
				}
			}
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/pre-post-trip-option',
				'attrs'        => $attrs,
				'innerContent' => [],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert simple_card_group block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_simple_card_group( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_component_background.field_component_background_value as background,
			field_simple_card_group_title.field_simple_card_group_title_value as title,
			field_simple_card_group_body.field_simple_card_group_body_value as description,
			(SELECT GROUP_CONCAT( field_simple_cards_target_id ORDER BY delta SEPARATOR ', ' ) FROM paragraph__field_simple_cards AS field_simple_cards WHERE paragraph.id = field_simple_cards.entity_id AND field_simple_cards.langcode = paragraph.langcode) AS simple_cards
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_component_background AS field_component_background ON paragraph.id = field_component_background.entity_id AND paragraph.langcode = field_component_background.langcode
				LEFT JOIN paragraph__field_simple_card_group_title AS field_simple_card_group_title ON paragraph.id = field_simple_card_group_title.entity_id AND paragraph.langcode = field_simple_card_group_title.langcode
				LEFT JOIN paragraph__field_simple_card_group_body AS field_simple_card_group_body ON paragraph.id = field_simple_card_group_body.entity_id AND paragraph.langcode = field_simple_card_group_body.langcode
		WHERE
			paragraph.type = 'simple_card_group' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch simple_card_group paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs                = [];
		$simple_cards_content = '';

		// Set attributes.
		$attrs['background']     = ! empty( $result['background'] ) ? strval( $result['background'] ) : '';
		$attrs['title']          = ! empty( $result['title'] ) ? strval( $result['title'] ) : '';
		$attrs['anchor']         = sanitize_title_with_dashes( $result['title'] );
		$attrs['titleAlignment'] = 'left';
		$attrs['headingLevel']   = 'h2';

		// Set description.
		if ( ! empty( $result['description'] ) ) {
			$attrs['description']    = wp_strip_all_tags( $result['description'] );
			$attrs['hasDescription'] = true;
		}

		// Check if simple_cards are available.
		if ( ! empty( $result['simple_cards'] ) ) {
			$simple_cards = explode( ',', $result['simple_cards'] );

			// Loop through each block.
			foreach ( $simple_cards as $simple_card ) {
				$simple_cards_content .= $this->convert_paragraph_simple_card( [ 'id' => $simple_card ] );
			}
		}

		// Check if title is set.
		if ( ! empty( $attrs['title'] ) ) {
			global $secondary_nav;

			// Add to secondary nav.
			$secondary_nav[] = [
				'title' => $attrs['title'],
				'url'   => get_wp_permalink( $attrs['anchor'] ),
			];
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/section',
				'attrs'        => $attrs,
				'innerContent' => [
					serialize_block(
						[
							'blockName'    => 'quark/media-description-cards',
							'attrs'        => [],
							'innerContent' => [ $simple_cards_content ],
						]
					),
				],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert slider block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_slider( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_component_background.field_component_background_value as background,
			field_slider_title.field_slider_title_value as title,
			field_slider_description.field_slider_description_value as description,
			(SELECT GROUP_CONCAT( field_slider_slide_target_id ORDER BY delta SEPARATOR ', ' ) FROM paragraph__field_slider_slide AS field_slider_slide WHERE paragraph.id = field_slider_slide.entity_id AND field_slider_slide.langcode = paragraph.langcode) AS slider_items
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_component_background AS field_component_background ON paragraph.id = field_component_background.entity_id AND paragraph.langcode = field_component_background.langcode
				LEFT JOIN paragraph__field_slider_title AS field_slider_title ON paragraph.id = field_slider_title.entity_id AND paragraph.langcode = field_slider_title.langcode
				LEFT JOIN paragraph__field_slider_description AS field_slider_description ON paragraph.id = field_slider_description.entity_id AND paragraph.langcode = field_slider_description.langcode
		WHERE
			paragraph.type = 'slider' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch slider paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs               = [];
		$slide_block_content = '';

		// Set attributes.
		$attrs['title']       = ! empty( $result['title'] ) ? strval( $result['title'] ) : '';
		$attrs['description'] = ! empty( $result['description'] ) ? strval( $result['description'] ) : '';
		$attrs['background']  = ! empty( $result['background'] ) ? strval( $result['background'] ) : '';

		// Check if slider_items are available.
		if ( ! empty( $result['slider_items'] ) ) {
			$slider_items = explode( ',', $result['slider_items'] );

			// Loop through each block.
			foreach ( $slider_items as $slider_item ) {
				$slide_block_content .= $this->convert_paragraph_slide( [ 'id' => $slider_item ] );
			}
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/slider',
				'attrs'        => $attrs,
				'innerContent' => [],
				'innerHTML'    => sprintf( '<div class="quark-slider--wrapper">%s</div>', $slide_block_content ),
			]
		) . PHP_EOL;
	}

	/**
	 * Convert slide block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_slide( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_slide_title.field_slide_title_value as title,
			field_slide_description.field_slide_description_value as description,
			field_slide_link.field_slide_link_title as link_title,
			field_slide_link.field_slide_link_uri as link,
			field_slide_image.field_slide_image_target_id as image
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_slide_title AS field_slide_title ON paragraph.id = field_slide_title.entity_id AND paragraph.langcode = field_slide_title.langcode
				LEFT JOIN paragraph__field_slide_description AS field_slide_description ON paragraph.id = field_slide_description.entity_id AND paragraph.langcode = field_slide_description.langcode
				LEFT JOIN paragraph__field_slide_link AS field_slide_link ON paragraph.id = field_slide_link.entity_id AND paragraph.langcode = field_slide_link.langcode
				LEFT JOIN paragraph__field_slide_image AS field_slide_image ON paragraph.id = field_slide_image.entity_id AND paragraph.langcode = field_slide_image.langcode
		WHERE
			paragraph.type = 'slide' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch slide paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs = [];

		// Set attributes.
		$attrs['title']        = ! empty( $result['title'] ) ? strval( $result['title'] ) : '';
		$attrs['subtitle']     = ! empty( $result['subtitle'] ) ? strval( $result['subtitle'] ) : '';
		$attrs['description']  = ! empty( $result['description'] ) ? strval( $result['description'] ) : '';
		$attrs['link']['url']  = ! empty( $result['link'] ) ? get_wp_permalink( strval( $result['link'] ) ) : '';
		$attrs['link']['text'] = ! empty( $result['link_title'] ) ? strval( $result['link_title'] ) : '';

		// Check if image is available.
		if ( ! empty( $result['image'] ) ) {
			$image_target_id = download_file_by_mid( absint( $result['image'] ) );

			// Check if image found.
			if ( ! $image_target_id instanceof WP_Error ) {
				$attrs['image'] = absint( $image_target_id );
			}
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/slide',
				'attrs'        => $attrs,
				'innerContent' => [],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert staff_member_list block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_staff_member_list( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_staff_list_title.field_staff_list_title_value as title,
			field_staff_list_description.field_staff_list_description_value as description,
			field_component_background.field_component_background_value as background,
			field_staff_list_view.field_staff_list_view_display_id as view,
			field_staff_list_view.field_staff_list_view_data as view_data
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_staff_list_title AS field_staff_list_title ON paragraph.id = field_staff_list_title.entity_id AND paragraph.langcode = field_staff_list_title.langcode
				LEFT JOIN paragraph__field_staff_list_description AS field_staff_list_description ON paragraph.id = field_staff_list_description.entity_id AND paragraph.langcode = field_staff_list_description.langcode
				LEFT JOIN paragraph__field_component_background AS field_component_background ON paragraph.id = field_component_background.entity_id AND paragraph.langcode = field_component_background.langcode
				LEFT JOIN paragraph__field_staff_list_view AS field_staff_list_view ON paragraph.id = field_staff_list_view.entity_id AND paragraph.langcode = field_staff_list_view.langcode
		WHERE
			paragraph.type = 'staff_member_list' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch staff_member_list paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs = [];

		// Set attributes.
		$attrs['title']       = ! empty( $result['title'] ) ? strval( $result['title'] ) : '';
		$attrs['description'] = ! empty( $result['description'] ) ? strval( $result['description'] ) : '';
		$attrs['background']  = ! empty( $result['background'] ) ? strval( $result['background'] ) : '';
		$attrs['view']        = ! empty( $result['view'] ) ? strval( $result['view'] ) : '';
		$attrs['viewData']    = ! empty( $result['view_data'] ) ? maybe_unserialize( $result['view_data'] ) : [];

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/staff-member-list',
				'attrs'        => $attrs,
				'innerContent' => [],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert summary_item block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_summary_item( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_summary_label.field_summary_label_value as title,
			field_summary_value.field_summary_value_value as description
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_summary_label AS field_summary_label ON paragraph.id = field_summary_label.entity_id AND paragraph.langcode = field_summary_label.langcode
				LEFT JOIN paragraph__field_summary_value AS field_summary_value ON paragraph.id = field_summary_value.entity_id AND paragraph.langcode = field_summary_value.langcode
		WHERE
			paragraph.type = 'summary_item' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch summary_item paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs = [];

		// Set attributes.
		$attrs['label'] = ! empty( $result['title'] ) ? strval( $result['title'] ) : '';
		$attrs['value'] = ! empty( $result['description'] ) ? strval( $result['description'] ) : '';

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/specifications-item',
				'attrs'        => $attrs,
				'innerContent' => [],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert summary_bar block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_summary_bar( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			(SELECT GROUP_CONCAT( field_summary_bar_item.field_summary_bar_item_target_id ORDER BY delta SEPARATOR ', ' ) FROM paragraph__field_summary_bar_item AS field_summary_bar_item WHERE paragraph.id = field_summary_bar_item.entity_id AND field_summary_bar_item.langcode = paragraph.langcode) AS summary_items
		FROM
			paragraphs_item_field_data AS paragraph
		WHERE
			paragraph.type = 'summary_bar' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch summary_bar paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$summary_items_content = '';

		// Check if summary_items are available.
		if ( ! empty( $result['summary_items'] ) ) {
			$summary_items = explode( ',', $result['summary_items'] );

			// Loop through each block.
			foreach ( $summary_items as $summary_item ) {
				$summary_items_content .= $this->convert_paragraph_summary_item( [ 'id' => $summary_item ] );
			}
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/specifications',
				'attrs'        => [
					'title' => 'Destination Specifications',
				],
				'innerContent' => [ $summary_items_content ],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert testimonial block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_testimonial( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_testimonial_title.field_testimonial_title_value as title,
			field_testimonial.field_testimonial_target_id as testimonial,
			(SELECT body_value FROM node__body WHERE node__body.entity_id = testimonial ) AS content,
			(SELECT field_testimonial_full_name_value FROM node__field_testimonial_full_name WHERE node__field_testimonial_full_name.entity_id = testimonial ) AS name

		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_testimonial_title AS field_testimonial_title ON paragraph.id = field_testimonial_title.entity_id AND paragraph.langcode = field_testimonial_title.langcode
				LEFT JOIN paragraph__field_testimonial AS field_testimonial ON paragraph.id = field_testimonial.entity_id AND paragraph.langcode = field_testimonial.langcode
		WHERE
			paragraph.type = 'testimonial' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch testimonial paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs             = [];
		$testimonial_block = '';

		// Set attributes.
		$attrs['title']          = ! empty( $result['title'] ) ? strval( $result['title'] ) : '';
		$attrs['anchor']         = sanitize_title_with_dashes( $result['title'] );
		$attrs['titleAlignment'] = 'left';
		$attrs['headingLevel']   = 'h2';

		// Check if testimonial is available.
		if ( ! empty( $result['testimonial'] ) ) {
			$review = prepare_content( strval( $result['content'] ) );

			// serialize review block.
			$card_review_block = serialize_block(
				[
					'blockName'    => 'quark/review-cards-review',
					'attrs'        => [],
					'innerContent' => [ $review ],
				]
			) . PHP_EOL;

			// serialize author block.
			$card_author_block = serialize_block(
				[
					'blockName'    => 'quark/review-cards-author',
					'attrs'        => [
						'author' => strval( $result['name'] ),
					],
					'innerContent' => [],
				]
			) . PHP_EOL;

			// serialize testimonial block.
			$testimonial_block = serialize_block(
				[
					'blockName'    => 'quark/review-cards',
					'attrs'        => [],
					'innerContent' => [
						serialize_block(
							[
								'blockName'    => 'quark/review-cards-card',
								'attrs'        => [],
								'innerContent' => [ $card_review_block . $card_author_block ],
							]
						),
					],
				]
			) . PHP_EOL;
		}

		// Check if title is set.
		if ( ! empty( $attrs['title'] ) ) {
			global $secondary_nav;

			// Add to secondary nav.
			$secondary_nav[] = [
				'title' => $attrs['title'],
				'url'   => get_wp_permalink( $attrs['anchor'] ),
			];
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/section',
				'attrs'        => $attrs,
				'innerContent' => [ $testimonial_block ],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert video block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_video( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_video.field_video_target_id as mid,
			media_field_data.thumbnail__target_id as fid,
			media_video_embed_field.field_media_video_embed_field_value as embed
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_video AS field_video ON paragraph.id = field_video.entity_id AND paragraph.langcode = field_video.langcode
				LEFT JOIN media_field_data ON field_video.field_video_target_id = media_field_data.mid
				LEFT JOIN media__field_media_video_embed_field AS media_video_embed_field ON field_video.field_video_target_id = media_video_embed_field.entity_id
		WHERE
			paragraph.type = 'video' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch video paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs = [];

		// Set attributes.
		$attrs['videoUrl'] = ! empty( $result['embed'] ) ? strval( $result['embed'] ) : '';

		// if mid is available.
		if ( ! empty( $result['mid'] ) ) {
			$image_target_id = download_file_by_mid( absint( $result['mid'] ) );

			// Get attachment src.
			$attachment_src = wp_get_attachment_image_src( absint( $image_target_id ), 'full' );

			// Check if attachment src is not available.
			if ( ! empty( $attachment_src ) ) {
				// Set image attributes.
				$attrs['image']   = [
					'id'     => $image_target_id,
					'src'    => $attachment_src[0],
					'width'  => $attachment_src[1],
					'height' => $attachment_src[2],
					'title'  => wp_get_attachment_caption( absint( $image_target_id ) ),
					'size'   => 'full',
				];
				$attrs['caption'] = wp_get_attachment_caption( absint( $image_target_id ) );
			}
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/fancy-video',
				'attrs'        => $attrs,
				'innerContent' => [],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert WYSIWYG block.
	 *
	 * @param array{}|array<int|string, string|int> $block Drupal block data.
	 *
	 * @return string
	 */
	public function convert_paragraph_wysiwyg( array $block = [] ): string {
		// Query.
		$query = "SELECT
			paragraph.id,
			paragraph.type,
			field_component_background.field_component_background_value as background,
			field_wysiwyg_title.field_wysiwyg_title_value as title,
			field_wysiwyg_body.field_wysiwyg_body_value as body
		FROM
			paragraphs_item_field_data AS paragraph
				LEFT JOIN paragraph__field_wysiwyg_title AS field_wysiwyg_title ON paragraph.id = field_wysiwyg_title.entity_id AND paragraph.langcode = field_wysiwyg_title.langcode
				LEFT JOIN paragraph__field_wysiwyg_body AS field_wysiwyg_body ON paragraph.id = field_wysiwyg_body.entity_id AND paragraph.langcode = field_wysiwyg_body.langcode
				LEFT JOIN paragraph__field_component_background AS field_component_background ON paragraph.id = field_component_background.entity_id AND paragraph.langcode = field_component_background.langcode
		WHERE
			paragraph.type = 'wysiwyg' AND paragraph.id = %s AND paragraph.langcode = 'en';";

		// Fetch data.
		$result = $this->database->get_row( $this->database->prepare( $query, $block['id'] ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::line( 'Unable to fetch wysiwyg paragraph data!' );

			// Bail out.
			return '';
		}

		// Block Markup.
		$attrs       = [];
		$result_body = trim( $result['body'] );

		// remove wrapper div from result body.
		$result_body = preg_replace( '/^<div[^>]*>(.*?)<\/div>$/is', '$1', $result_body );
		$markup      = prepare_content( strval( $result_body ) );

		// Check if title is empty.
		if ( ! empty( $result['title'] ) ) {
			$attrs['title']          = strval( $result['title'] );
			$attrs['anchor']         = sanitize_title_with_dashes( $result['title'] );
			$attrs['titleAlignment'] = 'left';
			$attrs['headingLevel']   = 'h2';
		} else {
			$attrs['hasTitle'] = false;
		}

		// Return data.
		return serialize_block(
			[
				'blockName'    => 'quark/section',
				'attrs'        => $attrs,
				'innerContent' => [ $markup ],
			]
		) . PHP_EOL;
	}

	/**
	 * Convert list of drupal images to collage block.
	 *
	 * @param array<int> $images List of image ids.
	 *
	 * @return string
	 */
	public function convert_images_to_collage( array $images = [] ): string {
		// Check if images are available.
		if ( empty( $images ) ) {
			return '';
		}

		// Collage media items.
		$collage_media_items = '';

		// Loop through each image.
		foreach ( $images as $image ) {
			$image_target_id = download_file_by_mid( absint( $image ) );

			// Check if image found.
			if ( $image_target_id instanceof WP_Error ) {
				continue;
			}

			// Set attributes.
			$attrs = [];

			// Get media type.
			$media_type = get_post_meta( $image_target_id, 'bundle', true );

			// Check if media type is remote video.
			if ( 'remote_video' === $media_type ) {
				$attrs['mediaType'] = 'video';

				// Get Video URL from Drupal.
				$video_url         = get_remote_video_url( absint( $image ) );
				$attrs['videoUrl'] = $video_url;
			}

			// Get attachment src.
			$attachment_src = wp_get_attachment_image_src( absint( $image_target_id ), 'full' );

			// Check if attachment src is not available.
			if ( empty( $attachment_src ) ) {
				continue;
			}

			// Set image attributes.
			$attrs['image']   = [
				'id'     => $image_target_id,
				'src'    => $attachment_src[0],
				'width'  => $attachment_src[1],
				'height' => $attachment_src[2],
				'title'  => wp_get_attachment_caption( absint( $image_target_id ) ),
				'size'   => 'full',
			];
			$attrs['caption'] = wp_get_attachment_caption( absint( $image_target_id ) );

			// If there is only one item in gallery and it is a remote video, set the block as fancy-video.
			if ( 1 === count( $images ) && 'remote_video' === $media_type ) {
				// Return data.
				return serialize_block(
					[
						'blockName'    => 'quark/section',
						'attrs'        => [
							'hasTitle' => false,
						],
						'innerContent' => [
							serialize_block(
								[
									'blockName'    => 'quark/fancy-video',
									'attrs'        => $attrs,
									'innerContent' => [],
								]
							),
						],
					]
				) . PHP_EOL;
			}

			// Build quark/collage-media-item block.
			$collage_media_items .= serialize_block(
				[
					'blockName'    => 'quark/collage-media-item',
					'attrs'        => $attrs,
					'innerContent' => [],
				]
			);
		}

		// Check if collage_media_items are empty.
		if ( empty( $collage_media_items ) ) {
			return '';
		}

		// Return collage block.
		return serialize_block(
			[
				'blockName'    => 'quark/collage',
				'attrs'        => [],
				'innerContent' => [
					$collage_media_items,
				],
			]
		) . PHP_EOL;
	}

	/**
	 * Prepare secondary nav.
	 *
	 * @param array<int, array<string, string>> $secondary_nav Secondary nav items.
	 *
	 * @return string
	 */
	public function prepare_secondary_nav( array $secondary_nav = [] ): string {
		// validate secondary_nav not empty.
		if ( empty( $secondary_nav ) ) {
			return '';
		}

		// Prepare secondary nav items.
		$secondary_nav_items = '';

		// Loop through secondary nav items.
		foreach ( $secondary_nav as $nav_item ) {
			// Prepare secondary nav item blocks.
			$secondary_nav_items .= serialize_block(
				[
					'blockName'    => 'quark/secondary-navigation-item',
					'attrs'        => [
						'title' => $nav_item['title'],
						'url'   => [
							'url'  => get_wp_permalink( $nav_item['url'] ),
							'text' => $nav_item['title'],
						],
					],
					'innerContent' => [],
				]
			);
		}

		// Check if secondary nav items are not empty.
		if ( empty( $secondary_nav_items ) ) {
			return '';
		}

		// Prepare secondary nav block.
		return serialize_block(
			[
				'blockName'    => 'quark/secondary-navigation',
				'attrs'        => [],
				'innerContent' => [
					serialize_block(
						[
							'blockName'    => 'quark/secondary-navigation-menu',
							'attrs'        => [],
							'innerContent' => [ $secondary_nav_items ],
						]
					),
				],
			]
		) . PHP_EOL;
	}
}
