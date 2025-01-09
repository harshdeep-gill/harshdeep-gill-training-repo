<?php
/**
 * Namespace functions.
 *
 * @package quark-multilingual
 */

namespace Quark\Multilingual;

use Inpsyde\MultilingualPress\NavMenu\ServiceProvider;
use WP_Post;

use function Travelopia\Multilingual\get_languages;
use function Travelopia\Multilingual\get_post_translations;
use function Travelopia\Multilingual\get_term_translations;
use function Travelopia\Multilingual\get_translated_image;
use function Travelopia\Translation\translate_strings;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Hooks and filter.
	add_filter( 'travelopia_translation_adapter', __NAMESPACE__ . '\\get_translation_adapter', 10, 1 );
	add_filter( 'travelopia_translation_output_strings', __NAMESPACE__ . '\\translate_block_strings', 10, 3 );
}

/**
 * Filter MultilingualPress modules.
 *
 * @param mixed[] $modules MultilingualPress modules.
 *
 * @return mixed[]
 */
function multilingualpress_modules( array $modules = [] ): array {
	// Check for modules.
	if ( empty( $modules ) ) {
		return $modules;
	}

	// Traverse modules.
	foreach ( $modules as $key => $module ) {
		// Remove Nav Menu modules.
		if ( $module instanceof ServiceProvider ) {
			unset( $modules[ $key ] );
		}
	}

	// Add custom MultilingualPress modules as their actual class names.
	$custom_module_classes = [
		'Post_Meta',
		'Term_Meta',
	];

	// Traverse module class names.
	foreach ( $custom_module_classes as $custom_module_class ) {
		// Get bootstrap file for module.
		$include = sprintf(
			'%s/multilingualpress-modules/class-%s.php',
			__DIR__,
			strtolower( str_replace( '_', '-', $custom_module_class ) )
		);

		// Get fully qualified class name for module instantiation.
		$fcqn = 'Quark\Multilingual\MultilingualPress_Modules\\' . $custom_module_class;

		// Add instance of module if it doesn't exist.
		if ( ! class_exists( $fcqn ) && ! isset( $modules[ $fcqn ] ) ) {
			require_once $include;

			// Add to modules.
			$modules[] = new $fcqn();
		}
	}

	// Return updated modules.
	return $modules;
}

/**
 * Get translation adapter.
 *
 * @return string Translation adapter.
 */
function get_translation_adapter(): string {
	// Return DeepL as translation adapter.
	return 'deepl';
}

/**
 * Update translated strings to include block translations.
 *
 * @param string[] $strings                Translated strings.
 * @param string   $language               Language.
 * @param string   $from_language          From language.
 * @param bool     $translate_dynamic_urls Translate dynamic URLs.
 *
 * @return string[]
 */
function translate_block_strings( array $strings = [], string $language = '', string $from_language = '', bool $translate_dynamic_urls = true ): array {
	// Check if we have strings.
	if ( empty( $strings ) || ! function_exists( 'Travelopia\Translation\translate_strings' ) ) {
		return $strings;
	}

	// Get site IDs from languages.
	$from_site_id = 0;
	$to_site_id   = 0;
	$languages    = get_languages();

	// Traverse languages to get the site IDs.
	if ( is_array( $languages ) ) {
		foreach ( $languages as $site_language ) {
			if ( empty( $site_language['language'] ) || empty( $site_language['site_id'] ) ) {
				continue;
			}

			// Check if this is the language we are translating to.
			if ( $site_language['language'] === $language ) {
				$to_site_id = absint( $site_language['site_id'] );
			}

			// Check if this is the language we are translating from.
			if ( $site_language['language'] === $from_language ) {
				$from_site_id = absint( $site_language['site_id'] );
			}
		}
	}

	// Block and attribute translation mapping.
	$block_attributes_to_translate = (array) apply_filters( 'qrk_translation_block_attributes', [] );

	// Traverse strings.
	foreach ( $strings as $key => $string ) {
		// Translate dynamic URLs from content.
		// Since classic post doesn't have blocks, process it before checking for blocks.
		if ( $translate_dynamic_urls ) {
			$strings[ $key ] = translate_dynamic_url_from_content( $strings[ $key ], $from_site_id, $to_site_id );
		}

		// Check if we have blocks in the first place.
		if ( ! has_blocks( $string ) ) {
			continue;
		}

		// Parse blocks.
		$parsed_blocks = parse_blocks( $string );

		// Skip if no parsed blocks found.
		if ( empty( $parsed_blocks ) ) {
			continue;
		}

		// Flatten blocks.
		$parsed_blocks = _flatten_blocks( $parsed_blocks );

		// Prepare translations.
		$translations = [];

		// Traverse blocks.
		foreach ( $parsed_blocks as $parsed_block ) {
			// Check if we have a valid block.
			if ( ! is_array( $parsed_block ) || empty( $parsed_block['blockName'] ) ) {
				continue;
			}

			// Check if this is one of our translatable blocks.
			if ( ! isset( $block_attributes_to_translate[ $parsed_block['blockName'] ] ) || ! is_array( $block_attributes_to_translate[ $parsed_block['blockName'] ] ) ) {
				continue;
			}

			// Text attributes to translate.
			if ( ! empty( $block_attributes_to_translate[ $parsed_block['blockName'] ]['text'] ) ) {
				// Get attributes to translate for this block.
				$attributes_to_translate = (array) $block_attributes_to_translate[ $parsed_block['blockName'] ]['text'];

				// Traverse translations.
				foreach ( $attributes_to_translate as $attribute ) {
					if ( empty( $parsed_block['attrs'][ $attribute ] ) ) {
						continue;
					}

					// Get translated value.
					$value = translate_strings( [ $parsed_block['attrs'][ $attribute ] ], $language, $from_language );

					// Check if we have a translated value and add to translations.
					if ( is_array( $value ) && ! empty( $value ) ) {
						$translations[] = [
							'source'      => $parsed_block['attrs'][ $attribute ],
							'translation' => $value[0],
						];
					}
				}
			}

			// Image attributes to translate.
			if ( ! empty( $block_attributes_to_translate[ $parsed_block['blockName'] ]['image'] ) ) {
				// Get attributes to translate for this block.
				$attributes_to_translate = (array) $block_attributes_to_translate[ $parsed_block['blockName'] ]['image'];

				// Traverse translations.
				foreach ( $attributes_to_translate as $attribute ) {
					if ( empty( $parsed_block['attrs'][ $attribute ] ) ) {
						continue;
					}

					// Check for array of IDs.
					if ( is_array( $parsed_block['attrs'][ $attribute ] ) && empty( $parsed_block['attrs'][ $attribute ]['id'] ) ) {
						// Traverse images.
						foreach ( $parsed_block['attrs'][ $attribute ] as $image ) {
							// Check if image is not empty.
							if ( empty( $image['id'] ) ) {
								continue;
							}

							// Get translated image.
							$original_image_id = absint( $image['id'] );
							$translated_image  = get_translated_image( $original_image_id, $from_site_id, $to_site_id );

							// Update translated image.
							if ( $translated_image instanceof WP_Post ) {
								$translations[] = [
									'source'      => '{"id":' . $original_image_id . ',',
									'translation' => '{"id":' . $translated_image->ID . ',',
								];
							}
						}
					} elseif ( ! empty( $parsed_block['attrs'][ $attribute ]['id'] ) ) {
						// Single Image ID.
						// Get translated image.
						$original_image_id = absint( $parsed_block['attrs'][ $attribute ]['id'] );
						$translated_image  = get_translated_image( $original_image_id, $from_site_id, $to_site_id );

						/**
						 * Update translated image, if found.
						 * This will cover 99.9% of cases.
						 * The only edge case that may occur is if similarly names attributes with IDs exist in blocks.
						 */
						if ( $translated_image instanceof WP_Post ) {
							$translations[] = [
								'source'      => $attribute . '":{"id":' . $original_image_id . ',',
								'translation' => $attribute . '":{"id":' . $translated_image->ID . ',',
							];
						}
					} elseif ( ! empty( $parsed_block['attrs']['id'] ) ) {
						// Core Image block.
						// Get translated image.
						$original_image_id = absint( $parsed_block['attrs']['id'] );
						$translated_image  = get_translated_image( $original_image_id, $from_site_id, $to_site_id );

						/**
						 * Update translated image, if found.
						 */
						if ( $translated_image instanceof WP_Post ) {
							// Change image ID.
							$translations[] = [
								'source'      => 'image {"id":' . $original_image_id . ',',
								'translation' => 'image {"id":' . $translated_image->ID . ',',
							];

							// Change image class.
							$translations[] = [
								'source'      => 'wp-image-' . $original_image_id,
								'translation' => 'wp-image-' . $translated_image->ID,
							];
						}
					}
				}
			}

			// Post ID attributes to translate.
			if ( ! empty( $block_attributes_to_translate[ $parsed_block['blockName'] ]['post_id'] ) ) {
				// Get attributes to translate for this block.
				$attributes_to_translate = (array) $block_attributes_to_translate[ $parsed_block['blockName'] ]['post_id'];

				// Traverse translations.
				foreach ( $attributes_to_translate as $attribute ) {
					if ( empty( $parsed_block['attrs'][ $attribute ] ) ) {
						continue;
					}

					// Check for array.
					if ( is_array( $parsed_block['attrs'][ $attribute ] ) ) {
						// Array of IDs.
						$translated_posts = [];

						// Traverse parsed block attributes.
						foreach ( $parsed_block['attrs'][ $attribute ] as $post_id ) {
							// Get original post and translations.
							$original_post_id  = absint( $post_id );
							$post_translations = get_post_translations( $original_post_id, $from_site_id );

							// Get translated post in "to" site.
							if ( ! empty( $post_translations ) ) {
								foreach ( $post_translations as $post_translation ) {
									if ( $to_site_id === $post_translation['site_id'] && ! empty( $post_translation['post_id'] ) ) {
										$translated_posts[] = absint( $post_translation['post_id'] );
										break;
									}
								}
							}
						}

						// Check if we have translated posts.
						if ( ! empty( $translated_posts ) ) {
							$translations[] = [
								'source'      => $attribute . '":[' . implode( ',', $parsed_block['attrs'][ $attribute ] ) . ']',
								'translation' => $attribute . '":[' . implode( ',', $translated_posts ) . ']',
							];
						}
					} else {
						// Single Post ID.
						// Get original post and translations.
						$original_post_id   = absint( $parsed_block['attrs'][ $attribute ] );
						$translated_post_id = 0;
						$post_translations  = get_post_translations( $original_post_id, $from_site_id );

						// Get translated post in "to" site.
						if ( ! empty( $post_translations ) ) {
							foreach ( $post_translations as $post_translation ) {
								if ( $to_site_id === $post_translation['site_id'] && ! empty( $post_translation['post_id'] ) ) {
									$translated_post_id = absint( $post_translation['post_id'] );
									break;
								}
							}
						}

						// Check if we have translated post ID.
						if ( ! empty( $translated_post_id ) ) {
							$translations[] = [
								'source'      => $attribute . '":' . $original_post_id,
								'translation' => $attribute . '":' . $translated_post_id,
							];
						}
					}
				}
			}

			// Term ID attributes to translate.
			if ( ! empty( $block_attributes_to_translate[ $parsed_block['blockName'] ]['term_id'] ) ) {
				// Get attributes to translate for this block.
				$attributes_to_translate = (array) $block_attributes_to_translate[ $parsed_block['blockName'] ]['term_id'];

				// Traverse translations.
				foreach ( $attributes_to_translate as $attribute ) {
					if ( empty( $parsed_block['attrs'][ $attribute ] ) ) {
						continue;
					}

					// Check for array.
					if ( is_array( $parsed_block['attrs'][ $attribute ] ) ) {
						// Array of IDs.
						$translated_terms = [];

						// Traverse parsed block attributes to get term translations.
						foreach ( $parsed_block['attrs'][ $attribute ] as $term_id ) {
							// Get original term and translations.
							$original_term_id  = absint( $term_id );
							$term_translations = get_term_translations( $original_term_id, $from_site_id );

							// Get translated term in "to" site.
							if ( ! empty( $term_translations ) ) {
								foreach ( $term_translations as $term_translation ) {
									if ( $to_site_id === $term_translation['site_id'] && ! empty( $term_translation['term_id'] ) ) {
										$translated_terms[] = absint( $term_translation['term_id'] );
										break;
									}
								}
							}
						}

						// Check if we have translated terms.
						if ( ! empty( $translated_terms ) ) {
							$translations[] = [
								'source'      => $attribute . '":[' . implode( ',', $parsed_block['attrs'][ $attribute ] ) . ']',
								'translation' => $attribute . '":[' . implode( ',', $translated_terms ) . ']',
							];
						}
					} else {
						// Single Post ID.
						// Get original term and translations.
						$original_term_id   = absint( $parsed_block['attrs'][ $attribute ] );
						$translated_term_id = 0;
						$term_translations  = get_term_translations( $original_term_id, $from_site_id );

						// Get translated term in "to" site.
						if ( ! empty( $term_translations ) ) {
							foreach ( $term_translations as $term_translation ) {
								if ( $to_site_id === $term_translation['site_id'] && ! empty( $term_translation['term_id'] ) ) {
									$translated_term_id = absint( $term_translation['term_id'] );
									break;
								}
							}
						}

						// Check if we have translated term ID.
						if ( ! empty( $translated_term_id ) ) {
							$translations[] = [
								'source'      => $attribute . '":' . $original_term_id,
								'translation' => $attribute . '":' . $translated_term_id,
							];
						}
					}
				}
			}

			// Object attributes to translate.
			if ( ! empty( $block_attributes_to_translate[ $parsed_block['blockName'] ]['object'] ) ) {
				// Get attributes to translate for this block.
				$attributes_to_translate = (array) $block_attributes_to_translate[ $parsed_block['blockName'] ]['object'];

				// Traverse translations.
				foreach ( $attributes_to_translate as $object_name => $object_keys ) {
					if ( ! is_array( $object_keys ) ) {
						continue;
					}

					// Look for objects to translate.
					foreach ( $object_keys as $object_key ) {
						if ( empty( $parsed_block['attrs'][ $object_name ] ) || empty( $parsed_block['attrs'][ $object_name ][ $object_key ] ) ) {
							continue;
						}

						// Get translated value from translation API.
						$value = translate_strings( [ $parsed_block['attrs'][ $object_name ][ $object_key ] ], $language, $from_language );

						// Check if we have a translated value and add to translations.
						if ( is_array( $value ) && ! empty( $value ) ) {
							$translations[] = [
								'source'      => '"' . $parsed_block['attrs'][ $object_name ][ $object_key ] . '"',
								'translation' => '"' . $value[0] . '"',
							];
						}
					}
				}
			}
		}

		// Check if we have strings to translate.
		if ( ! empty( $translations ) ) {
			// Traverse translations.
			foreach ( $translations as $translation ) {
				// Validate translations.
				if ( empty( $translation['source'] ) || empty( $translation['translation'] ) || $translation['source'] === $translation['translation'] ) {
					continue;
				}

				// Update translations in strings.
				$strings[ $key ] = str_replace( $translation['source'], $translation['translation'], $strings[ $key ] );
			}
		}
	}

	// Return updated strings.
	return $strings;
}

/**
 * Translate dynamic URL from content.
 *
 * @param string $content      The content from where URLs need to translate.
 * @param int    $from_site_id From Site ID.
 * @param int    $to_site_id   To Site ID.
 *
 * @return string Translated content.
 */
function translate_dynamic_url_from_content( string $content = '', int $from_site_id = 0, int $to_site_id = 0 ): string {
	// Check if we have content, from site ID and to site ID.
	if ( empty( $content ) || empty( $from_site_id ) || empty( $to_site_id ) ) {
		return $content;
	}

	// Get home URL.
	$home_url = get_home_url( $from_site_id );
	$home_url = rtrim( $home_url, '/' );

	// Regex to find dynamic links within content.
	$regex = '/"(?P<url>(?:' . preg_quote( $home_url, '/' ) . ')\/post\/(?P<post_id>[0-9]+)(?P<query_params>[#|?].+)?)"/mU';

	// Decode the quotes before parseing the content.
	$content_to_parse = str_replace( '\u0022', '"', $content );

	// Look for dynamic links within content.
	preg_match_all( $regex, $content_to_parse, $matches );

	// Check if we have matches.
	if ( ! empty( $matches['post_id'] ) ) {
		// Get URLs and post IDs.
		$urls     = $matches['url'];
		$post_ids = $matches['post_id'];

		// Links found, traverse them.
		foreach ( $post_ids as $match_key => $post_id ) {
			// Get original post and translations.
			$original_post_id   = absint( $post_id );
			$translated_post_id = 0;
			$post_translations  = get_post_translations( $original_post_id, $from_site_id );
			$query_params       = $matches['query_params'][ $match_key ] ?? '';

			// Get translated post in "to" site.
			if ( ! empty( $post_translations ) ) {
				foreach ( $post_translations as $post_translation ) {
					if ( $to_site_id === $post_translation['site_id'] && ! empty( $post_translation['post_id'] ) ) {
						$translated_post_id = absint( $post_translation['post_id'] );
						break;
					}
				}
			}

			// Get original URL.
			$original_url = $urls[ $match_key ];

			// Check if we have a translated post ID.
			if ( ! empty( $translated_post_id ) ) {
				// Update translated URL.
				$translated_url = home_url( "/post/$translated_post_id" . $query_params );
			} else {
				$translated_url = get_fallback_url();
			}

			// Update translations in strings.
			$content = str_replace(
				strval( $original_url ),
				strval( $translated_url ),
				$content
			);
		}
	}

	// Return updated content.
	return $content;
}

/**
 * Get fallback URL.
 *
 * @return string
 */
function get_fallback_url(): string {
	// Return home URL.
	return home_url( '/broken-url' );
}
