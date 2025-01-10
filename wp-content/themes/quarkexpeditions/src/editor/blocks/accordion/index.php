<?php
/**
 * Block Name: Accordion.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Accordion;

use WP_Block;

const BLOCK_NAME = 'quark/accordion';
const COMPONENT  = 'parts.accordion';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register the block.
	register_block_type_from_metadata(
		__DIR__,
		[
			'render_callback'   => __NAMESPACE__ . '\\render',
			'skip_inner_blocks' => true,
		]
	);

	// Add block attributes to translate.
	add_filter( 'qrk_translation_block_attributes', __NAMESPACE__ . '\\block_attributes_to_translate' );
}

/**
 * Render this block.
 *
 * @param mixed[]  $attributes The block attributes.
 * @param string   $content    The block content.
 * @param WP_Block $block      The block instance.
 *
 * @return string The block markup.
 */
function render( array $attributes = [], string $content = '', WP_Block $block = null ): string {
	// Check for block.
	if ( ! $block instanceof WP_Block ) {
		return $content;
	}

	// Build component attributes.
	$component_attributes = [
		'has_border' => $attributes['hasBorder'],
		'items'      => [],
	];

	// Build FAQ Schema.
	$add_faq_schema = $attributes['faqSchema'];
	$faq_schema     = [];

	// Items.
	foreach ( $block->inner_blocks as $inner_block ) {
		// Check for block.
		if ( ! $inner_block instanceof WP_Block ) {
			continue;
		}

		// Check if inner block is for accordion item and has title.
		if ( 'quark/accordion-item' !== $inner_block->name ) {
			continue;
		}

		// Initialize current item.
		$current_item = [];

		// Add title.
		$current_item['title'] = $inner_block->attributes['title'] ?? '';

		// Add content.
		$current_item['content'] = implode( '', array_map( 'render_block', $inner_block->parsed_block['innerBlocks'] ) );

		// Add isOpen attribute.
		$current_item['open'] = $inner_block->attributes['isOpen'] ?? false;

		// Add current item to array of items.
		$component_attributes['items'][] = $current_item;

		// Add FAQ schema items.
		if ( $add_faq_schema ) {
			$faq_schema[] = [
				'@type'          => 'Question',
				'name'           => $current_item['title'],
				'acceptedAnswer' => [
					'@type' => 'Answer',
					'text'  => wp_strip_all_tags( $current_item['content'] ),
				],
			];
		}
	}

	// Add FAQ schema.
	add_faq_schema( $faq_schema );

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}

/**
 * Add FAQ structured data schema.
 *
 * @param mixed[] $faq_schema FAQ schema to add.
 *
 * @return void
 */
function add_faq_schema( array $faq_schema = [] ): void {
	// Check if we have data.
	if ( empty( $faq_schema ) ) {
		return;
	}

	// Hook into the schema hook and add FAQ schema.
	add_filter(
		'travelopia_seo_structured_data_schema',
		static function ( array $schema = [] ) use ( $faq_schema ) {
			// Check if FAQPage already exists, and append questions-answers to existing FAQPage schema.
			$schema_already_added = false;

			// Check and build the schema markup.
			if ( ! empty( $schema ) || ! is_array( $schema ) ) {
				foreach ( $schema as $key => $schema_item ) {
					if (
						! is_array( $schema_item )
						|| empty( $schema_item['@type'] )
						|| 'FAQPage' !== $schema_item['@type']
						|| empty( $schema_item['mainEntity'] )
						|| ! is_array( $schema[ $key ] )
					) {
						continue;
					}

					// Populate the data.
					$main_entity                  = $schema_item['mainEntity'];
					$question_answers             = array_merge( $main_entity, $faq_schema );
					$schema[ $key ]['mainEntity'] = $question_answers;

					// Update already exists.
					$schema_already_added = true;
				}
			}

			// Schema not already added, add it now.
			if ( ! $schema_already_added ) {
				$schema[] = [
					'@context'   => 'https://schema.org',
					'@type'      => 'FAQPage',
					'mainEntity' => $faq_schema,
				];
			}

			// Return updated schema.
			return $schema;
		}
	);
}

/**
 * Block attributes that need to be translatable.
 *
 * @param mixed[] $blocks_and_attributes Blocks and attributes.
 *
 * @return mixed[]
 */
function block_attributes_to_translate( array $blocks_and_attributes = [] ): array {
	// Add data to translate.
	$blocks_and_attributes[ BLOCK_NAME . '-item' ] = [
		'text' => [ 'title' ],
	];

	// Return updated data.
	return $blocks_and_attributes;
}
