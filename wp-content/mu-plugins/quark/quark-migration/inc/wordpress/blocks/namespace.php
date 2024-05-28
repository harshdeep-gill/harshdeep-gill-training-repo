<?php
/**
 * Blocks.
 *
 * @package quark-migration
 *
 * phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
 */

namespace Quark\Migration\WordPress\Blocks;

use DOMDocument;
use DOMElement;

use function Quark\Migration\WordPress\convert_node_to_block;
use function Quark\Migration\WordPress\convert_to_blocks;

/**
 * Bootstrap.
 *
 * @return void
 */
function bootstrap(): void {
	// Add filters.
	add_filter( 'qrk_convert_to_blocks_fallback', __NAMESPACE__ . '\\convert_node_html', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_h1', __NAMESPACE__ . '\\convert_node_heading', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_h2', __NAMESPACE__ . '\\convert_node_heading', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_h3', __NAMESPACE__ . '\\convert_node_heading', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_h4', __NAMESPACE__ . '\\convert_node_heading', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_h5', __NAMESPACE__ . '\\convert_node_heading', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_h6', __NAMESPACE__ . '\\convert_node_heading', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_p', __NAMESPACE__ . '\\convert_node_paragraph', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_ul', __NAMESPACE__ . '\\convert_node_list', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_ol', __NAMESPACE__ . '\\convert_node_list', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_figure', __NAMESPACE__ . '\\convert_node_figure', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_img', __NAMESPACE__ . '\\convert_node_image', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_div', __NAMESPACE__ . '\\convert_node_div', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_a', __NAMESPACE__ . '\\convert_node_link', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_iframe', __NAMESPACE__ . '\\convert_node_iframe', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_table', __NAMESPACE__ . '\\convert_node_table', 10, 2 );
	add_filter( 'qrk_convert_to_blocks_blockquote', __NAMESPACE__ . '\\convert_node_blockquote', 10, 2 );
}

/**
 * Serialize a block.
 *
 * @param mixed[] $block Block data.
 *
 * @return string
 */
function serialize_block( array $block = [] ): string {
	// Check for block name.
	if ( empty( $block['blockName'] ) || ! is_string( $block['blockName'] ) ) {
		return '';
	}

	// Get block name.
	$name = $block['blockName'];

	// Remove core/ prefix and get the block name.
	if ( str_starts_with( $name, 'core/' ) ) {
		$name = substr( $name, strlen( 'core/' ) );
	}

	// Prepare opening tag suffix.
	if ( empty( $block['attrs'] ) ) {
		$opening_tag_suffix = '';
	} else {
		$opening_tag_suffix = ' ' . wp_json_encode( $block['attrs'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE );
	}

	// Replace encoded ampersands.
	$opening_tag_suffix = str_replace( 'u0026', '&', $opening_tag_suffix );

	// Prepare block output.
	if ( empty( $block['innerHTML'] ) ) {
		// Return empty block output.
		return sprintf(
			'<!-- wp:%s%s /-->',
			$name,
			$opening_tag_suffix
		);
	} else {
		// Return block output with inner block content.
		return sprintf(
			'<!-- wp:%1$s%2$s -->' . "\n" . '%3$s' . "\n" . '<!-- /wp:%1$s -->',
			$name,
			$opening_tag_suffix,
			$block['innerHTML']
		);
	}
}

/**
 * Convert an HTML node into a block.
 *
 * @param string          $output Block output.
 * @param DOMElement|null $node   Node element.
 *
 * @return string
 */
function convert_node_html( string $output = '', ?DOMElement $node = null ): string {
	// Check for node.
	if ( ! $node instanceof DOMElement ) {
		return $output;
	}

	// Prepare inner HTML.
	$inner_html = '';

	// Get inner HTML.
	foreach ( $node->childNodes as $child_node ) {
		if ( $child_node->ownerDocument instanceof DOMDocument ) {
			$inner_html .= $child_node->ownerDocument->saveHTML( $child_node );
		}
	}

	// Create a block if we had inner HTML.
	if ( ! empty( $inner_html ) ) {
		$output = serialize_block(
			[
				'blockName' => 'core/freeform',
				'innerHTML' => sprintf(
					'<%1$s>%2$s</%1$s>',
					$node->tagName,
					$inner_html
				),
			]
		);
	}

	// Return block output.
	return $output;
}

/**
 * Convert a heading node into a block.
 *
 * @param string          $output Block output.
 * @param DOMElement|null $node   Node element.
 *
 * @return string
 */
function convert_node_heading( string $output = '', ?DOMElement $node = null ): string {
	// Check for node.
	if ( ! $node instanceof DOMElement ) {
		return $output;
	}

	// Get tag name.
	$tag_name = $node->tagName;

	// Don't allow H1 in post content.
	if ( 'h1' === $tag_name ) {
		$tag_name = 'h2';
	}

	// Prepare inner HTML.
	$inner_html = '';

	// Get inner HTML.
	foreach ( $node->childNodes as $child_node ) {
		// If any child is an IFRAME, convert this into an IFRAME block.
		if ( $child_node instanceof DOMElement && 'iframe' === $child_node->tagName ) {
			return convert_node_iframe( '', $child_node );
		}

		// Get HTML of current child node.
		if ( $child_node->ownerDocument instanceof DOMDocument ) {
			$inner_html .= $child_node->ownerDocument->saveHTML( $child_node );
		}
	}

	// Return block output.
	return serialize_block(
		[
			'blockName' => 'core/heading',
			'attrs'     => [
				'level' => absint( str_replace( 'h', '', $tag_name ) ),
			],
			'innerHTML' => sprintf(
				'<%1$s class="wp-block-heading">%2$s</%1$s>',
				$tag_name,
				$inner_html
			),
		]
	);
}

/**
 * Convert a paragraph node into a block.
 *
 * @param string          $output Block output.
 * @param DOMElement|null $node   Node element.
 *
 * @return string
 */
function convert_node_paragraph( string $output = '', ?DOMElement $node = null ): string {
	// Check for node.
	if ( ! $node instanceof DOMElement ) {
		return $output;
	}

	/**
	 * If the only child inside a P is one of these tags, convert it to a block.
	 */
	if ( 1 === $node->childNodes->length && $node->childNodes[0] instanceof DOMElement ) {
		// If the only child inside a P is one of these tags.
		$special_tags = [
			'img',
		];

		// Look for special tags.
		if ( in_array( $node->childNodes[0]->tagName, $special_tags, true ) ) {
			return convert_node_to_block( $node->childNodes[0] );
		} elseif (
			'a' === $node->childNodes[0]->tagName
			&& str_contains( $node->childNodes[0]->getAttribute( 'class' ), 'btn btn-primary' )
		) {
			// If the only child has button classes.
			$button = convert_node_link( '', $node->childNodes[0] );

			// Return button block.
			if ( ! empty( $button ) ) {
				return $button;
			}
		}
	}

	// Prepare inner HTML.
	$inner_html = '';

	// Traverse child nodes.
	foreach ( $node->childNodes as $child_node ) {
		// If any child is a button, convert this into a button block.
		if (
			$child_node instanceof DOMElement
			&& 'a' === $child_node->tagName
			&& str_contains( $child_node->getAttribute( 'class' ), 'btn btn-primary' )
		) {
			$button = convert_node_link( '', $child_node );

			// check if button is not empty.
			if ( ! empty( $button ) ) {
				return $button;
			}
		}

		// if any child is an image, convert this into an image block.
		if ( $child_node instanceof DOMElement && 'img' === $child_node->tagName ) {
			$inner_html .= convert_node_to_block( $child_node );
		}

		// If any child is an IFRAME, convert this into an IFRAME block.
		if ( $child_node instanceof DOMElement && 'iframe' === $child_node->tagName ) {
			return convert_node_iframe( '', $child_node );
		}

		// Get HTML of current child node.
		if ( $child_node->ownerDocument instanceof DOMDocument ) {
			$inner_html .= $child_node->ownerDocument->saveHTML( $child_node );
		}
	}

	// Create a block if we had inner HTML.
	if ( ! empty( $inner_html ) ) {
		$output = serialize_block(
			[
				'blockName' => 'core/paragraph',
				'innerHTML' => '<p>' . $inner_html . '</p>',
			]
		);
	}

	// Return block output.
	return $output;
}

/**
 * Convert a list node into a block.
 *
 * @param string          $output Block output.
 * @param DOMElement|null $node   Node element.
 *
 * @return string
 */
function convert_node_list( string $output = '', ?DOMElement $node = null ): string {
	// Check for node.
	if ( ! $node instanceof DOMElement ) {
		return $output;
	}

	// Prepare inner HTML.
	$inner_html = '';

	// Get inner HTML.
	foreach ( $node->childNodes as $child_node ) {
		// get HTML of current child node.
		if ( $child_node->ownerDocument instanceof DOMDocument ) {
			$inner_html .= $child_node->ownerDocument->saveHTML( $child_node );
		}
	}

	// Create a block if we had inner HTML.
	if ( ! empty( $inner_html ) ) {
		$attrs = [];

		// Check for ordered list tag.
		if ( 'ol' === $node->tagName ) {
			$attrs['ordered'] = true;
		}

		// Create list block.
		$output = serialize_block(
			[
				'blockName' => 'core/list',
				'attrs'     => $attrs,
				'innerHTML' => sprintf(
					'<%1$s>%2$s</%1$s>',
					$node->tagName,
					$inner_html
				),
			]
		);
	}

	// Return block output.
	return $output;
}

/**
 * Convert a figure node into a block.
 *
 * @param string          $output Block output.
 * @param DOMElement|null $node   Node element.
 *
 * @return string
 */
function convert_node_figure( string $output = '', ?DOMElement $node = null ): string {
	// Check for node.
	if ( ! $node instanceof DOMElement ) {
		return $output;
	}

	// Get caption and image.
	$caption = '';
	$img     = null;

	// Traverse child nodes.
	foreach ( $node->childNodes as $child_node ) {
		if ( ! $child_node instanceof DOMElement ) {
			continue;
		}

		// Check for figcaption tags.
		if ( 'figcaption' === $child_node->tagName ) {
			foreach ( $child_node->childNodes as $grandchild_node ) {
				if ( $grandchild_node->ownerDocument instanceof DOMDocument ) {
					$caption .= $grandchild_node->ownerDocument->saveHTML( $grandchild_node );
				}
			}
		} elseif ( 'img' === $child_node->tagName ) {
			$img = $child_node;
		}
	}

	// Build image block with caption.
	if ( $img instanceof DOMElement ) {
		$output = convert_node_image( $output, $img, $caption );
	}

	// Return block output.
	return $output;
}

/**
 * Convert an image node into a block.
 *
 * @param string          $output  Block output.
 * @param DOMElement|null $node    Node element.
 * @param string          $caption Caption.
 *
 * @return string
 */
function convert_node_image( string $output = '', ?DOMElement $node = null, string $caption = '' ): string {
	// Check for node.
	if ( ! $node instanceof DOMElement ) {
		return $output;
	}

	// Get SRC.
	$src = $node->getAttribute( 'src' );

	// if src attribute is empty, bail out.
	if ( empty( $src ) ) {
		return $output;
	}

	// Look for image ID from class.
	$id    = 0;
	$class = $node->getAttribute( 'class' );

	// Check for image ID in class.
	if ( ! empty( $class ) && str_contains( $class, 'wp-image-' ) ) {
		preg_match( '#wp-image-([0-9])#', $class, $matches );

		// Check for matches.
		if ( ! empty( $matches[1] ) ) {
			$id = absint( $matches[1] );
		}
	}

	// Get other attributes.
	$alt    = $node->getAttribute( 'alt' );
	$width  = absint( $node->getAttribute( 'width' ) );
	$height = absint( $node->getAttribute( 'height' ) );

	// Build caption.
	$caption_string = '';

	// Check for caption is set.
	if ( ! empty( $caption ) ) {
		$caption_string = sprintf( '<figcaption class="wp-element-caption">%s</figcaption>', $caption );
	}

	// Check if we found an ID.
	if ( ! empty( $id ) ) {
		// We found an ID, treat it like an internal image.
		$output = serialize_block(
			[
				'blockName' => 'core/image',
				'attrs'     => [
					'id'              => $id,
					'sizeSlug'        => 'large',
					'linkDestination' => 'none',
				],
				'innerHTML' => sprintf(
					'<figure class="wp-block-image size-large"><img src="%s" alt="%s" class="wp-image-%d" />%s</figure>',
					$src,
					$alt,
					$id,
					$caption_string,
				),
			]
		);
	} else {
		// No ID found, treat it like an external image.
		$output = serialize_block(
			[
				'blockName' => 'core/image',
				'attrs'     => [
					'sizeSlug' => 'large',
				],
				'innerHTML' => sprintf(
					'<figure class="wp-block-image size-large"><img src="%s" alt="%s" width="%s" height="%s" />%s</figure>',
					$src,
					! empty( $alt ) ? $alt : '',
					! empty( $width ) ? $width : '',
					! empty( $height ) ? $height : '',
					$caption_string,
				),
			]
		);
	}

	// Return block output.
	return $output;
}

/**
 * Convert a DIV node into a block.
 *
 * @param string          $output Block output.
 * @param DOMElement|null $node   Node element.
 *
 * @return string
 */
function convert_node_div( string $output = '', ?DOMElement $node = null ): string {
	// Check for node.
	if ( ! $node instanceof DOMElement ) {
		return $output;
	}

	// Look for columns.
	$columns = [];

	// Look for cases where we'd need a freeform block.
	$freeform = false;

	// Traverse child nodes.
	foreach ( $node->childNodes as $child_node ) {
		if ( $child_node instanceof DOMElement && 'table' === $child_node->tagName ) {
			$freeform = true;
		} elseif ( $child_node instanceof DOMElement && 'div' === $child_node->tagName ) {
			$class = $child_node->getAttribute( 'class' );

			// Check for columns in classes.
			if ( ! empty( $class ) && str_contains( $class, 'col-' ) ) {
				// Column found, create block.
				$column_content = '';
				$attrs          = [];
				$html_attrs     = 'class="wp-block-column"';

				// Get column content.
				foreach ( $child_node->childNodes as $grandchild_node ) {
					if ( $grandchild_node->ownerDocument instanceof DOMDocument ) {
						$column_content .= $grandchild_node->ownerDocument->saveHTML( $grandchild_node );
					}
				}

				// Check for custom markup.
				if ( str_contains( $column_content, '<div' ) ) {
					// Block appears to have custom markup,
					// Wrap it in a freeform block.
					$column_content = serialize_block(
						[
							'blockName' => 'core/freeform',
							'innerHTML' => $column_content,
						]
					);
				} else {
					// Convert content into blocks.
					$column_content = convert_to_blocks( $column_content );

					// Check if we have blocks.
					if ( ! has_blocks( $column_content ) ) {
						// Column content doesn't have blocks.
						// Wrap it in a freeform block, or else the Core Columns
						// block will throw an error.
						$column_content = serialize_block(
							[
								'blockName' => 'core/freeform',
								'innerHTML' => $column_content,
							]
						);
					}
				}

				// Determine column width.
				if ( str_contains( $class, 'col-sm-3' ) || str_contains( $class, 'col-md-3' ) ) {
					$attrs['width'] = '25%';
					$html_attrs    .= ' style="flex-basis:33.33%"';
				} elseif ( str_contains( $class, 'col-sm-2' ) || str_contains( $class, 'col-md-2' ) ) {
					$attrs['width'] = '16.66%';
					$html_attrs    .= ' style="flex-basis:16.66%"';
				} elseif ( str_contains( $class, 'col-sm-8' ) || str_contains( $class, 'col-md-8' ) ) {
					$attrs['width'] = '66.66%';
					$html_attrs    .= ' style="flex-basis:66.66%"';
				} elseif ( str_contains( $class, 'col-sm-9' ) || str_contains( $class, 'col-md-9' ) ) {
					$attrs['width'] = '75%';
					$html_attrs    .= ' style="flex-basis:75%"';
				}

				// Add column block.
				$columns[] = serialize_block(
					[
						'blockName' => 'core/column',
						'attrs'     => $attrs,
						'innerHTML' => sprintf(
							'<div %s>%s</div>',
							$html_attrs,
							$column_content
						),
					]
				);
			}
		}
	}

	// No columns found, return the div as is.
	if ( empty( $columns ) ) {
		if ( $node->ownerDocument instanceof DOMDocument ) {
			if ( true === $freeform ) {
				return serialize_block(
					[
						'blockName' => 'core/freeform',
						'innerHTML' => strval( $node->ownerDocument->saveHTML( $node ) ),
					]
				);
			} else {
				// Return div block.
				return strval( $node->ownerDocument->saveHTML( $node ) );
			}
		} else {
			// Return empty string.
			return $output;
		}
	}

	// Return columns block.
	return serialize_block(
		[
			'blockName' => 'core/columns',
			'innerHTML' => sprintf(
				'<div class="wp-block-columns">%s</div>',
				implode( "\n\n", $columns )
			),
		]
	);
}

/**
 * Convert a Table node into a block.
 *
 * @param string          $output Block output.
 * @param DOMElement|null $node   Node element.
 *
 * @return string
 */
function convert_node_table( string $output = '', ?DOMElement $node = null ): string {
	// Check for node.
	if ( ! $node instanceof DOMElement ) {
		return $output;
	}

	// Check for table class.
	if ( $node->ownerDocument instanceof DOMDocument ) {
		return serialize_block(
			[
				'blockName' => 'core/table',
				'innerHTML' => '<figure class="wp-block-table">' . $node->ownerDocument->saveHTML( $node ) . '</figure>',
			]
		);
	} else {
		// Return empty string.
		return $output;
	}
}

/**
 * Convert a link node into a block.
 *
 * @param string          $output Block output.
 * @param DOMElement|null $node   Node element.
 *
 * @return string
 */
function convert_node_link( string $output = '', DOMElement $node = null ): string {
	// Check for correct node.
	if ( ! $node instanceof DOMElement ) {
		return $output;
	}

	// get class attribute.
	$class = $node->getAttribute( 'class' );

	// Check for button classes.
	if ( ! str_contains( $class, 'btn btn-primary' ) ) {
		// No class found, just return normal link.
		return $output;
	}

	// Return columns block.
	return serialize_block(
		[
			'blockName' => 'core/buttons',
			'innerHTML' => sprintf(
				'<div class="wp-block-buttons">%s</div>',
				serialize_block(
					[
						'blockName' => 'core/button',
						'innerHTML' => sprintf(
							'<div class="wp-block-button"><a class="wp-block-button__link" href="%s">%s</a></div>',
							$node->getAttribute( 'href' ),
							$node->textContent
						),
					]
				)
			),
		]
	);
}

/**
 * Convert an IFRAME node into a block.
 *
 * @param string          $output Block output.
 * @param DOMElement|null $node   Node element.
 *
 * @return string
 */
function convert_node_iframe( string $output = '', ?DOMElement $node = null ): string {
	// Check for node.
	if ( ! $node instanceof DOMElement ) {
		return $output;
	}

	// Get SRC.
	$src = $node->getAttribute( 'src' );
	$url = wp_parse_url( $src );

	// Check $url is an array.
	if ( ! is_array( $url ) ) {
		return $output;
	}

	// Build src attribute.
	$url = wp_parse_args(
		$url,
		[
			'scheme' => 'https',
			'host'   => '',
			'path'   => '',
			'query'  => '',
		]
	);
	$src = $url['scheme'] . '://' . $url['host'] . $url['path'] . ( $url['query'] ? '?' . $url['query'] : '' );

	// Ignore video series embeds, as there is no proper support in WordPress.
	if ( str_contains( $src, 'embed/videoseries' ) ) {
		return $output;
	}

	// Check if we support it.
	if ( str_contains( $src, 'youtube' ) ) {
		// YouTube.
		$src = str_replace( 'youtube-nocookie.com', 'youtube.com', $src );
		$src = str_replace( '?rel=0', '', $src );
		$src = str_replace( '&rel=0', '', $src );

		// Build and return block output.
		return serialize_block(
			[
				'blockName' => 'core/embed',
				'attrs'     => [
					'url'              => $src,
					'type'             => 'video',
					'providerNameSlug' => 'youtube',
					'responsive'       => true,
					'className'        => 'wp-embed-aspect-16-9 wp-has-aspect-ratio',
				],
				'innerHTML' => sprintf(
					"<figure class=\"wp-block-embed is-type-video is-provider-youtube wp-block-embed-youtube wp-embed-aspect-16-9 wp-has-aspect-ratio\"><div class=\"wp-block-embed__wrapper\">\n%s\n</div></figure>",
					$src,
				),
			]
		);
	} elseif ( str_contains( $src, 'vimeo' ) ) {
		// Vimeo.
		return serialize_block(
			[
				'blockName' => 'core/embed',
				'attrs'     => [
					'url'              => $src,
					'type'             => 'video',
					'providerNameSlug' => 'vimeo',
					'responsive'       => true,
					'className'        => 'wp-embed-aspect-16-9 wp-has-aspect-ratio',
				],
				'innerHTML' => sprintf(
					"<figure class=\"wp-block-embed is-type-video is-provider-vimeo wp-block-embed-vimeo wp-embed-aspect-16-9 wp-has-aspect-ratio\"><div class=\"wp-block-embed__wrapper\">\n%s\n</div></figure>",
					$src,
				),
			]
		);
	} else {
		// Unknown src.
		// Build and return block output.
		return serialize_block(
			[
				'blockName' => 'core/embed',
				'attrs'     => [
					'url'        => $src,
					'type'       => 'rich',
					'responsive' => true,
				],
				'innerHTML' => sprintf(
					"<figure class=\"wp-block-embed is-type-rich\"><div class=\"wp-block-embed__wrapper\">\n%s\n</div></figure>",
					$src,
				),
			]
		);
	}
}

/**
 * Convert a blockquote node into a block.
 *
 * @param string          $output Block output.
 * @param DOMElement|null $node   Node element.
 *
 * @return string
 */
function convert_node_blockquote( string $output = '', ?DOMElement $node = null ): string {
	// Check for correct node.
	if ( ! $node instanceof DOMElement ) {
		return $output;
	}

	// get class attribute.
	$class = $node->getAttribute( 'class' );

	// Check for instagram embed classes.
	if ( str_contains( $class, 'instagram-media' ) ) {
		// Get instagram URL.
		$regex = '/<a\s+(?:[^>]*?\s+)?href=([\'"])(.*?)\1/';

		// Check for owner document.
		if ( $node->ownerDocument instanceof DOMDocument ) {
			$instagram_dom = $node->ownerDocument->saveHTML( $node );

			// Get the matches.
			preg_match_all( $regex, strval( $instagram_dom ), $matches );

			// Check for matches.
			if ( $matches[2] ) {
				// Return instagram block.
				return serialize_block(
					[
						'blockName' => 'core/embed',
						'attrs'     => [
							'url'        => $matches[2][0],
							'type'       => 'rich',
							'responsive' => true,
						],
						'innerHTML' => sprintf(
							"<figure class=\"wp-block-embed is-type-rich\"><div class=\"wp-block-embed__wrapper\">\n%s\n</div></figure>",
							$matches[2][0],
						),
					]
				);
			}
		}
	} else {
		// Prepare inner HTML.
		$inner_html = '';

		// Get inner HTML.
		foreach ( $node->childNodes as $child_node ) {
			// Check for correct node.
			if ( $child_node instanceof DOMElement ) {
				$inner_html .= convert_node_to_block( $child_node );
			}
		}

		// No instagram embed, return blockquote.
		return serialize_block(
			[
				'blockName' => 'core/quote',
				'innerHTML' => '<blockquote class="wp-block-quote">' . $inner_html . '</blockquote>',
			]
		);
	}

	// Fallback.
	return $output;
}
