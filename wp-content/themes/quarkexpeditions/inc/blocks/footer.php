<?php
/**
 * Block: Footer.
 *
 * @package quark
 */

namespace Quark\Theme\Blocks\Footer;

const BLOCK_NAME = 'quark/footer';
const COMPONENT  = 'parts.footer';

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
	if ( BLOCK_NAME !== $block['blockName'] ) {
		return $content;
	}

	// Build the attributes.
	$attributes = [
		'top'    => [],
		'middle' => [],
		'bottom' => [],
	];

	// Check if innerblocks are available.
	if ( ! empty( $block['innerBlocks'] ) ) {
		// Loop through the footer sections.
		foreach ( $block['innerBlocks'] as $footer_section ) {
			if ( ! in_array(
				$footer_section['blockName'],
				[
					'quark/footer-top',
					'quark/footer-middle',
					'quark/footer-bottom',
				],
				true
			) ) {
				continue;
			}

			// Cases for the different sections.
			switch ( $footer_section['blockName'] ) {
				// Footer top.
				case 'quark/footer-top':
					// Check if inner blocks are present.
					if ( ! empty( $footer_section['innerBlocks'] ) ) {
						// Loop through the inner blocks.
						foreach ( $footer_section['innerBlocks'] as $top_inner_block ) {
							$processed_column = process_column_block( $top_inner_block );

							// Check if column is empty.
							if ( ! empty( $processed_column ) ) {
								$attributes['top'][] = $processed_column;
							}
						}
					}
					break;

				// Footer middle.
				case 'quark/footer-middle':
					// Check if inner blocks are present.
					if ( ! empty( $footer_section['innerBlocks'] ) ) {
						// Loop through the inner blocks.
						foreach ( $footer_section['innerBlocks'] as $middle_inner_block ) {
							// Check if it is a column.
							if ( 'quark/footer-column' === $middle_inner_block['blockName'] ) {
								$processed_column = process_column_block( $middle_inner_block );

								// Check if column is empty.
								if ( ! empty( $processed_column ) ) {
									$attributes['middle'][] = $processed_column;
								}
							}

							// Check if it is a navigation block.
							if ( 'quark/footer-navigation' === $middle_inner_block['blockName'] ) {
								$processed_navigation = process_navigation_block( $middle_inner_block );

								// Check if column is empty.
								if ( ! empty( $processed_navigation ) ) {
									$attributes['middle'][] = $processed_navigation;
								}
							}
						}
					}
					break;

				// Footer bottom.
				case 'quark/footer-bottom':
					if ( ! empty( $footer_section['innerBlocks'] ) ) {
						// Loop through the inner blocks.
						foreach ( $footer_section['innerBlocks'] as $bottom_inner_block ) {
							// Check if it is a navigation block.
							if ( 'quark/footer-navigation' === $bottom_inner_block['blockName'] ) {
								$processed_navigation = process_navigation_block( $bottom_inner_block );

								// Check if column is empty.
								if ( ! empty( $processed_navigation ) ) {
									$attributes['bottom'][] = $processed_navigation;
								}
							}

							// Check if it is a copyright block.
							if ( 'quark/footer-copyright' === $bottom_inner_block['blockName'] ) {
								$copyright = [
									'type'    => 'copyright',
									'content' => [],
								];

								// Check if inner blocks exist.
								if ( ! empty( $bottom_inner_block['innerBlocks'] ) ) {
									// Loop through inner blocks.
									foreach ( $bottom_inner_block['innerBlocks'] as $maybe_copyright_text ) {
										if ( 'core/paragraph' !== $maybe_copyright_text['blockName'] ) {
											continue;
										}

										// Add the content.
										$copyright['content'][] = render_block( $maybe_copyright_text );
									}
								}

								// Append to the column.
								$attributes['bottom'][] = $copyright;
							}
						}
					}
					break;
			}
		}
	}

	// Return rendered component.
	return quark_get_component( COMPONENT, $attributes );
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
					'quark/button',
					'quark/footer-logo',
					'core/paragraph',
					'core/list',
					'core/heading',
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
					$payment_options = [
						'type'    => 'payment-options',
						'content' => [],
					];

					// Check if inner blocks exist.
					if ( ! empty( $column_inner_block['innerBlocks'] ) ) {
						// Loop over inner blocks.
						foreach ( $column_inner_block['innerBlocks'] as $maybe_payment_option ) {
							if ( 'quark/footer-payment-option' !== $maybe_payment_option['blockName'] ) {
								continue;
							}

							// Append to the parent.
							$payment_options['content'][] = [
								'type'       => 'payment-option',
								'attributes' => [
									'type' => $maybe_payment_option['attrs']['type'],
								],
							];
						}
					}

					// Append to the column.
					$column['content'][] = $payment_options;
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
