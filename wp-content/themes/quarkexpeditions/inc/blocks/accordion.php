<?php
/**
 * Block Name: Accordion.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Accordion;

const BLOCK_NAME = 'quark/accordion';
const COMPONENT  = 'parts.accordion';

/**
 * Bootstrap this block.
 *
 * @return void
 */
function bootstrap(): void {
	// Register this block only on the front-end.
	add_action( 'template_redirect', __NAMESPACE__ . '\\register' );
}

/**
 * Register block on the front-end.
 *
 * @return void
 */
function register(): void {
	// Fire hooks.
	add_filter( 'pre_render_block', __NAMESPACE__ . '\\render', 10, 2 );
}

/**
 * Render this block.
 *
 * @param string|null $content Original content.
 * @param mixed[]     $block   Parsed block.
 *
 * @return null|string
 */
function render( ?string $content = null, array $block = [] ): null|string {
	// Check for block.
	if ( BLOCK_NAME !== $block['blockName'] || empty( $block['innerBlocks'] ) || ! is_array( $block['innerBlocks'] ) ) {
		return $content;
	}

	// Build component attributes.
	$attributes = [
		'has_border' => $block['attrs']['hasBorder'] ?? true,
		'items'      => [],
	];

	// Build FAQ Schema.
	$add_faq_schema = $block['attrs']['faqSchema'] ?? false;
	$faq_schema     = [];

	// Items.
	foreach ( $block['innerBlocks'] as $inner_block ) {
		// Check if inner block is for accordion item and has title.
		if (
			'quark/accordion-item' !== $inner_block['blockName'] ||
			empty( $inner_block['attrs']['title'] )
		) {
			continue;
		}

		// Initialize current item.
		$current_item = [];

		// Add title.
		$current_item['title'] = $inner_block['attrs']['title'] ?? '';

		// Add content.
		$current_item['content'] = implode( '', array_map( 'render_block', $inner_block['innerBlocks'] ) );

		// Add isOpen attribute.
		$current_item['open'] = $inner_block['attrs']['isOpen'] ?? false;

		// Add current item to array of items.
		$attributes['items'][] = $current_item;

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
	return quark_get_component( COMPONENT, $attributes );
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
