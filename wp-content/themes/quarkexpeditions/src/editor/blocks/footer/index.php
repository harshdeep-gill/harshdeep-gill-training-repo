<?php
/**
 * Block: Footer.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Footer;

use WP_Block;
use WP_Block_List;

const COMPONENT = 'parts.footer';

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

	// Build the component attributes.
	$component_attributes = [
		'top'    => [],
		'middle' => [],
		'bottom' => [],
	];

	// Check if innerblocks are available.
	if ( $block->inner_blocks instanceof WP_Block_List ) {
		// Loop through the footer sections.
		foreach ( $block->inner_blocks as $footer_section ) {
			// Check for Block.
			if ( ! $footer_section instanceof WP_Block ) {
				continue;
			}

			// Check for the section name.
			if ( ! in_array(
				$footer_section->name,
				[
					'quark/footer-top',
					'quark/footer-middle',
					'quark/footer-bottom',
				],
				true
			) ) {
				continue;
			}

			// Check for the section name.
			// Cases for the different sections.
			switch ( $footer_section->name ) {
				// Footer top.
				case 'quark/footer-top':
					// Check if inner blocks are present.
					if ( $footer_section->inner_blocks instanceof WP_Block_List ) {
						// Loop through the inner blocks.
						foreach ( $footer_section->inner_blocks as $top_inner_block ) {
							// Check for Block.
							if ( ! $top_inner_block instanceof WP_Block ) {
								continue;
							}

							// Process the column block.
							$processed_column = process_column_block( $top_inner_block->parsed_block );

							// Check if column is empty.
							if ( ! empty( $processed_column ) ) {
								$component_attributes['top'][] = $processed_column;
							}
						}
					}
					break;

				// Footer middle.
				case 'quark/footer-middle':
					// Check if inner blocks are present.
					if ( $footer_section->inner_blocks instanceof WP_Block_List ) {
						// Loop through the inner blocks.
						foreach ( $footer_section->inner_blocks as $middle_inner_block ) {
							// Check for Block.
							if ( ! $middle_inner_block instanceof WP_Block ) {
								continue;
							}

							// Check if it is a column.
							if ( 'quark/footer-column' === $middle_inner_block->name ) {
								$processed_column = process_column_block( $middle_inner_block->parsed_block );

								// Check if column is empty.
								if ( ! empty( $processed_column ) ) {
									$component_attributes['middle'][] = $processed_column;
								}
							}

							// Check if it is a navigation block.
							if ( 'quark/footer-navigation' === $middle_inner_block->name ) {
								$processed_navigation = process_navigation_block( $middle_inner_block->parsed_block );

								// Check if column is empty.
								if ( ! empty( $processed_navigation ) ) {
									$component_attributes['middle'][] = $processed_navigation;
								}
							}
						}
					}
					break;

				// Footer bottom.
				case 'quark/footer-bottom':
					if ( $footer_section->inner_blocks instanceof WP_Block_List ) {
						// Loop through the inner blocks.
						foreach ( $footer_section->inner_blocks as $bottom_inner_block ) {
							// Check for Block.
							if ( ! $bottom_inner_block instanceof WP_Block ) {
								continue;
							}

							// Check if it is a navigation block.
							if ( 'quark/footer-navigation' === $bottom_inner_block->name ) {
								$processed_navigation = process_navigation_block( $bottom_inner_block->parsed_block );

								// Check if column is empty.
								if ( ! empty( $processed_navigation ) ) {
									$component_attributes['bottom'][] = $processed_navigation;
								}
							}

							// Check if it is a copyright block.
							if ( 'quark/footer-copyright' === $bottom_inner_block->name ) {
								$copyright = [
									'type'    => 'copyright',
									'content' => [],
								];

								// Check if inner blocks exist.
								if ( $bottom_inner_block->inner_blocks instanceof WP_Block_List ) {
									// Loop through inner blocks.
									foreach ( $bottom_inner_block->inner_blocks as $maybe_copyright_text ) {
										// Check for Block.
										if ( ! $maybe_copyright_text instanceof WP_Block ) {
											continue;
										}

										// Check for the paragraph block.
										if ( 'core/paragraph' !== $maybe_copyright_text->name ) {
											continue;
										}

										// Add the content.
										$copyright['content'][] = render_block( $maybe_copyright_text->parsed_block );
									}
								}

								// Append to the column.
								$component_attributes['bottom'][] = $copyright;
							}
						}
					}
					break;
			}
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $component_attributes );
}

/**
 * Processes the Column block to be used at multiple places.
 *
 * @param mixed[] $block The block to process.
 *
 * @return mixed[] The processed block data.
 */
function process_column_block( array $block = [] ): array {
	// Check if it is a column.
	if ( 'quark/footer-column' !== $block['blockName'] ) {
		return [];
	}

	// Initialize the column.
	$column = [
		'type'       => 'column',
		'attributes' => [
			'url' => $block['attrs']['url']['url'] ?? '',
		],
		'content'    => [],
	];

	// Check if inner blocks are present.
	if ( ! empty( $block['innerBlocks'] ) ) {
		// Loop through inner blocks.
		foreach ( $block['innerBlocks'] as $column_inner_block ) {
			if ( ! in_array(
				$column_inner_block['blockName'],
				[
					'quark/footer-column-title',
					'quark/footer-social-links',
					'quark/footer-icon',
					'quark/footer-payment-options',
					'quark/footer-associations',
					'quark/button',
					'quark/footer-logo',
					'core/paragraph',
					'core/list',
					'core/heading',
					'quark/currency-switcher',
				],
				true
			) ) {
				continue;
			}

			// Cases for different inner blocks.
			switch ( $column_inner_block['blockName'] ) {
				// Title.
				case 'quark/footer-column-title':
					$column['content'][] = [
						'type'       => 'title',
						'attributes' => [
							'title' => $column_inner_block['attrs']['title'] ?? '',
						],
					];
					break;

				// Footer Icon.
				case 'quark/footer-icon':
					$column['content'][] = [
						'type'       => 'icon',
						'attributes' => [
							'name' => $column_inner_block['attrs']['type'] ?? '',
						],
					];
					break;

				// Social Links.
				case 'quark/footer-social-links':
					$social_links = [
						'type'       => 'social-links',
						'attributes' => [
							'social_links' => [],
						],
					];

					// Check if inner blocks exist.
					if ( ! empty( $column_inner_block['innerBlocks'] ) ) {
						// Loop through the inner blocks.
						foreach ( $column_inner_block['innerBlocks'] as $maybe_social_link ) {
							// Check for inner block type.
							if ( 'quark/footer-social-link' !== $maybe_social_link['blockName'] ) {
								continue;
							}

							// Append the social link.
							if ( ! empty( $maybe_social_link['attrs']['type'] ) && ! empty( $maybe_social_link['attrs']['url']['url'] ) ) {
								$social_links['attributes']['social_links'][ $maybe_social_link['attrs']['type'] ] = $maybe_social_link['attrs']['url']['url'];
							}
						}
					}

					// Add to the column.
					$column['content'][] = $social_links;
					break;

				// Footer payment options.
				case 'quark/footer-payment-options':
					$column['content'][] = [ 'type' => 'payment-options' ];
					break;

				// Association Links.
				case 'quark/footer-associations':
					$association_links = [
						'type'       => 'associations',
						'attributes' => [
							'association_links' => [],
						],
					];

					$association_links['test'] = 'yes';
					// Check if inner blocks exist.
					if ( ! empty( $column_inner_block['innerBlocks'] ) ) {
						// Loop through the inner blocks.
						foreach ( $column_inner_block['innerBlocks'] as $maybe_association_link ) {
							// Check for inner block type.
							if ( 'quark/footer-association-link' !== $maybe_association_link['blockName'] ) {
								continue;
							}

							// Append the Association link.
							if ( ! empty( $maybe_association_link['attrs']['type'] ) && ! empty( $maybe_association_link['attrs']['url']['url'] ) ) {
								$association_links['attributes']['association_links'][ $maybe_association_link['attrs']['type'] ] = $maybe_association_link['attrs']['url']['url'];
							}
						}
					}

					// Add to the column.
					$column['content'][] = $association_links;
					break;

				// Footer logo.
				case 'quark/footer-logo':
					$column['content'][] = [ 'type' => 'logo' ];
					break;

				// Quark button.
				case 'quark/button':
					$column['content'][] = [
						'type'    => 'button',
						'content' => render_block( $column_inner_block ),
					];
					break;

				// Quark Currency Switcher.
				case 'quark/currency-switcher':
					$column['content'][] = [
						'type'    => 'currency',
						'content' => render_block( $column_inner_block ),
					];
					break;

				// Rest of the cases.
				default:
					$column['content'][] = [
						'type'    => 'core',
						'content' => render_block( $column_inner_block ),
					];
			}
		}
	}

	// Return the processed column.
	return $column;
}

/**
 * Processes the Navigation block to be used at multiple places.
 *
 * @param mixed[] $block The block to process.
 *
 * @return mixed[] The processed block data.
 */
function process_navigation_block( array $block = [] ): array {
	// Check if it is a navigation.
	if ( 'quark/footer-navigation' !== $block['blockName'] ) {
		return [];
	}

	// Initialize the navigation.
	$navigation = [
		'type'       => 'navigation',
		'attributes' => [
			'title' => $block['attrs']['title'] ?? '',
		],
		'content'    => [],
	];

	// Check if inner blocks are present.
	if ( ! empty( $block['innerBlocks'] ) ) {
		// Loop through the inner blocks.
		foreach ( $block['innerBlocks'] as $maybe_navigation_item ) {
			if ( 'quark/footer-navigation-item' !== $maybe_navigation_item['blockName'] ) {
				continue;
			}

			// Append to the navigation container.
			$navigation['content'][] = [
				'type'       => 'navigation-item',
				'attributes' => [
					'title'  => $maybe_navigation_item['attrs']['title'] ?? '',
					'url'    => $maybe_navigation_item['attrs']['url']['url'] ?? '',
					'target' => ! empty( $maybe_navigation_item['attrs']['url']['newWindow'] ) ? '_blank' : '',
				],
			];
		}
	}

	// Return the processed navigation block.
	return $navigation;
}
