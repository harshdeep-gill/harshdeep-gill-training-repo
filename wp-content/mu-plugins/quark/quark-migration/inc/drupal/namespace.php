<?php
/**
 * Drupal functions.
 *
 * @package quark-migration
 */

namespace Quark\Migration\Drupal;

use wpdb;
use WP_Error;
use WP_Post;
use WP_Query;
use WP_Term_Query;
use WP_Term;
use WP_CLI;

use function Quark\Migration\WordPress\convert_to_blocks;

/**
 * Get the Drupal database object.
 *
 * @return wpdb
 */
function get_database(): wpdb {
	// Drupal database instance.
	static $drupal_db = null;

	// Bail out if const are not defined.
	if (
		! defined( 'QUARK_MIGRATION_DB_USER' ) ||
		! defined( 'QUARK_MIGRATION_DB_PASSWORD' ) ||
		! defined( 'QUARK_MIGRATION_DB_NAME' ) ||
		! defined( 'QUARK_MIGRATION_DB_HOST' )
	) {
		return $drupal_db;
	}

	// Check if we have instance of Drupal database or not. If not then create one.
	if ( null === $drupal_db ) {
		$drupal_db = new wpdb(
			QUARK_MIGRATION_DB_USER,
			QUARK_MIGRATION_DB_PASSWORD,
			QUARK_MIGRATION_DB_NAME,
			QUARK_MIGRATION_DB_HOST,
		);
	}

	// Return Drupal database instance.
	return $drupal_db;
}

/**
 * Stuff to run before migration.
 *
 * @return void
 */
function prepare_for_migration(): void {
	// Define const if not already.
	if ( ! defined( 'WP_POST_REVISIONS' ) ) {
		define( 'WP_POST_REVISIONS', 0 );
	}

	// jpeg quality.
	add_filter(
		'jpeg_quality',
		function () {
			return 100;
		}
	);
}

/**
 * Download file by Drupal fid (file ID).
 *
 * @param int $drupal_fid Drupal file ID.
 *
 * @return int|WP_Error WordPress's attachment ID on success otherwise error.
 */
function download_file_by_fid( int $drupal_fid = 0 ): int|WP_Error {
	// Check the Drupal fid.
	if ( empty( $drupal_fid ) ) {
		return 0;
	}

	// Drupal database instance.
	$drupal_db = get_database();

	// Build and execute query.
	$file_data = $drupal_db->get_row(
		strval(
			$drupal_db->prepare(
				'SELECT * FROM file_managed WHERE file_managed.fid=%d',
				absint( $drupal_fid )
			)
		),
		ARRAY_A
	);

	// If not data found then bail out.
	if ( empty( $file_data ) || ! is_array( $file_data ) ) {
		return 0;
	}

	// Download the file and send attachment ID.
	return download_file( $file_data );
}

/**
 * Download file by Drupal mid (Media ID).
 *
 * @param int $drupal_mid Drupal media ID.
 *
 * @return int|WP_Error WordPress's attachment ID on success otherwise error.
 */
function download_file_by_mid( int $drupal_mid = 0 ): int|WP_Error {
	// Check the Drupal mid.
	if ( empty( $drupal_mid ) ) {
		return 0;
	}

	// Drupal database instance.
	$drupal_db = get_database();

	// Build and execute query.
	$file_data = $drupal_db->get_row(
		strval(
			$drupal_db->prepare(
				'
				SELECT
					file_managed.*,
					media_field_data.*
				FROM
					file_managed
					LEFT JOIN media_field_data ON media_field_data.thumbnail__target_id = file_managed.fid
				WHERE
					media_field_data.mid=%d
			',
				absint( $drupal_mid )
			)
		),
		ARRAY_A
	);

	// If not data found then bail out.
	if ( empty( $file_data ) || ! is_array( $file_data ) ) {
		return 0;
	}

	// Download the file and send attachment ID.
	return download_file( $file_data );
}

/**
 * Download file by Drupal URL.
 *
 * @param string $url Drupal URL.
 *
 * @return int|WP_Error WordPress's attachment ID on success otherwise error.
 */
function download_file_by_url( string $url = '' ): int|WP_Error {
	// Check the Drupal URL.
	if ( empty( $url ) ) {
		return 0;
	}

	// Convert URL to Drupal public path.
	$url = str_replace( '/sites/default/files/', 'public://', urldecode( $url ) );

	// Drupal database instance.
	$drupal_db = get_database();

	// Build and execute query.
	$file_data = $drupal_db->get_row(
		strval(
			$drupal_db->prepare(
				'
				SELECT
					file_managed.*,
					media_field_data.*
				FROM
					file_managed
					LEFT JOIN media_field_data ON media_field_data.thumbnail__target_id = file_managed.fid
				WHERE
					file_managed.uri=%s
			',
				$url
			)
		),
		ARRAY_A
	);

	// If not data found then bail out.
	if ( empty( $file_data ) || ! is_array( $file_data ) ) {
		return 0;
	}

	// Download the file and send attachment ID.
	return download_file( $file_data );
}

/**
 * Get WordPress attachment ID by Drupal media path.
 *
 * @param string $drupal_path Drupal media (relative) path.
 *
 * @return int WordPress's attachment ID on success otherwise 0.
 */
function get_wp_attachment_id_by_drupal_url( string $drupal_path = '' ): int {
	// Global $wpdb instance.
	global $wpdb;

	// Check whether path is provided or not.
	if ( empty( $drupal_path ) ) {
		return 0;
	}

	// Get attachment ID.
	$attachment = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT
				$wpdb->posts.ID AS id
			FROM
				$wpdb->posts
				INNER JOIN $wpdb->postmeta AS `postmeta` ON $wpdb->posts.ID = postmeta.post_id
			WHERE
				$wpdb->posts.post_type = 'attachment'
				AND $wpdb->posts.post_status = 'inherit'
				AND ( postmeta.meta_key = 'drupal_path' AND postmeta.meta_value = %s )
			",
			[
				$drupal_path,
			]
		),
		ARRAY_A
	);

	// If attachment ID found then send it.
	if ( ! empty( $attachment['id'] ) ) {
		return absint( $attachment['id'] );
	}

	// If attachment ID not found then return 0.
	return 0;
}

/**
 * Download the file.
 *
 * @param array<string, mixed> $file_data File data.
 *
 * @return int|WP_Error
 */
function download_file( array $file_data = [] ): int|WP_Error {
	// Drupal FID, and MID.
	$drupal_fid = absint( $file_data['fid'] );
	$drupal_mid = absint( $file_data['mid'] ?? 0 );
	$bundle     = $file_data['bundle'] ?? 'image';

	// Get attachment if exists.
	$wp_attachment = get_post_by_id( drupal_id: $drupal_fid, post_type: 'attachment', meta_key: 'drupal_fid' );

	// If Attachment already exists then bail out.
	if ( ! empty( $wp_attachment ) ) {
		return $wp_attachment->ID;
	}

	// if uri starts with private:// then bail out.
	if ( is_string( $file_data['uri'] ) && str_starts_with( $file_data['uri'], 'private://' ) ) {
		return new WP_Error( 'QUARK_migration_media_private', 'The media file is private.', $file_data['uri'] );
	}

	// Get file name.
	$file_name = ltrim(
		str_replace( 'public://', '', strval( $file_data['uri'] ?? '' ) ),
		'/'
	);

	// Check media file directory is defined or not.
	if ( defined( 'QUARK_MIGRATION_MEDIA_PATH' ) && ! empty( QUARK_MIGRATION_MEDIA_PATH ) ) {
		$path = rtrim( QUARK_MIGRATION_MEDIA_PATH, '/' ) . '/' . $file_name;

		// If File is not exists then bail out.
		if ( ! file_exists( $path ) ) {
			return new WP_Error( 'QUARK_migration_media_not_exists', 'The media file does not exist at this path.', $path );
		}

		// Create temporary file.
		$info = pathinfo( $file_name );
		$tmp  = '/tmp/' . wp_generate_password( 12, false ) . '.' . ( $info['extension'] ?? '' );
		copy( $path, $tmp );
	} else {
		// decode special characters.
		$special_chars = [
			':'  => '%3A',
			'?'  => '%3F',
			'='  => '%3D',
			'&'  => '%26',
			'%'  => '%25',
			'#'  => '%23',
			'+'  => '%2B',
			'@'  => '%40',
			'$'  => '%24',
			','  => '%2C',
			';'  => '%3B',
			'['  => '%5B',
			']'  => '%5D',
			'{'  => '%7B',
			'}'  => '%7D',
			'|'  => '%7C',
			'\\' => '%5C',
			'"'  => '%22',
			"'"  => '%27',
			'`'  => '%60',
			' '  => '%20',
		];
		$file_name     = strtr( $file_name, $special_chars );

		// Download the file.
		$tmp = download_url( 'https://www.quarkexpeditions.com/sites/default/files/' . $file_name );

		// If Failed to download media file then bail out.
		if ( $tmp instanceof WP_Error || ! is_string( $tmp ) ) {
			return new WP_Error( 'QUARK_migration_media_download_failed', 'Failed to download media file.', 'https://www.quarkexpeditions.com/sites/default/files/' . $file_name );
		}
	}

	// Post name.
	$post_name = ! empty( $drupal_mid ) ? "image-$drupal_fid-$drupal_mid" : "image-$drupal_fid";

	// Attachment data.
	$post_data = [
		'post_author' => 1,
		'post_name'   => $post_name,
		'meta_input'  => [
			'drupal_fid' => $drupal_fid,
			'bundle'     => $bundle,
		],
	];

	// Media ID.
	if ( ! empty( $drupal_mid ) ) {
		$post_data['meta_input']['drupal_mid'] = $drupal_mid;
	}

	// Media Alt Text.
	if ( ! empty( $file_data['field_media_image_alt'] ) ) {
		$post_data['meta_input']['_wp_attachment_image_alt'] = trim( strval( $file_data['field_media_image_alt'] ) );
	}

	// Media Title.
	if ( ! empty( $file_data['field_media_image_title'] ) ) {
		$post_data['post_title'] = trim( strval( $file_data['field_media_image_title'] ) );
	} elseif ( ! empty( $file_data['name'] ) ) {
		$post_data['post_title'] = trim( strval( $file_data['name'] ) );
	}

	// Media Caption.
	if ( ! empty( $file_data['field_image_caption_value'] ) ) {
		$post_data['post_excerpt'] = trim( strval( $file_data['field_image_caption_value'] ) );
	}

	// Media Description.
	if ( ! empty( $file_data['field_media_file_description'] ) ) {
		$post_data['post_content'] = trim( strval( $file_data['field_media_file_description'] ) );
	}

	// Photographer Credit.
	if ( ! empty( $file_data['field_photographer_credit_value'] ) ) {
		$post_data['meta_input']['photographer_credit'] = trim( strval( $file_data['field_photographer_credit_value'] ) );
	}

	// Create WordPress attachment.
	return media_handle_sideload(
		[
			'name'     => basename( $file_name ),
			'tmp_name' => $tmp,
		],
		0,
		null,
		$post_data
	);
}

/**
 * Get Drupal media by URL.
 * Some Drupal media in content are not tagged with drupal_mid or drupal_fid.
 * This function will try to get the media based on URL.
 *
 * @param string $url Drupal media path.
 *
 * @return int|WP_Error
 */
function get_media_by_url( string $url = '' ): int|WP_Error {
	// Check the URL.
	if ( empty( $url ) ) {
		return new WP_Error( 'QUARK_migration_media_url_empty', 'The media URL is empty.', $url );
	}

	// Check valid media url, starts with /sites/default/files/.
	if ( ! str_starts_with( $url, '/sites/default/files/' ) ) {
		return new WP_Error( 'QUARK_migration_media_url_invalid', 'The media URL is invalid.', $url );
	}

	// Check media is already imported.
	$attachment = get_wp_attachment_id_by_drupal_url( $url );

	// If attachment found then return it.
	if ( ! empty( $attachment ) ) {
		return $attachment;
	}

	// Download the file and send attachment ID.
	$tmp = download_url( 'https://www.quarkexpeditions.com' . $url );

	// If Failed to download media file then bail out.
	if ( $tmp instanceof WP_Error ) {
		return new WP_Error( 'QUARK_migration_media_download_failed', 'Failed to download media file by URL.', 'https://www.quarkexpeditions.com' . $url );
	}

	// Parse image SRC.
	$parsed_src = wp_parse_url( 'https://www.quarkexpeditions.com' . $url );

	// Ignore external images and invalid paths.
	if (
		! is_array( $parsed_src )
		|| empty( $parsed_src['path'] )
		|| ! str_contains( $parsed_src['path'], '/sites/default' )
	) {
		$post_name = sanitize_title_with_dashes( str_replace( '/sites/default/', '', $url ) );
	} else {
		// Get post name.
		$post_name = basename( $parsed_src['path'] );
	}

	// Attachment data.
	$post_data = [
		'post_author' => 1,
		'post_name'   => $post_name,
		'post_title'  => $post_name,
		'meta_input'  => [
			'drupal_path' => $url,
			'bundle'      => 'image',
		],
	];

	// Create WordPress attachment.
	return media_handle_sideload(
		[
			'name'     => $post_name,
			'tmp_name' => $tmp,
		],
		0,
		null,
		$post_data
	);
}

/**
 * Get a WordPress term based on its Drupal ID.
 *
 * @param int    $drupal_id Drupal ID.
 * @param string $taxonomy  WordPress taxonomy.
 *
 * @return false|WP_Term
 */
function get_term_by_id( int $drupal_id = 0, string $taxonomy = '' ): false|WP_Term {
	// Query term.
	$term = new WP_Term_Query(
		[
			'taxonomy'   => $taxonomy,
			'number'     => 1,
			'hide_empty' => false,
			'meta_query' => [
				[
					'key'     => 'drupal_term_id',
					'value'   => $drupal_id,
					'compare' => '=',
				],
			],
		]
	);

	// If no term found then bail out.
	if ( empty( $term->terms ) ) {
		return false;
	}

	// Return term.
	return $term->terms[0];
}

/**
 * Get a WordPress post based on its Drupal ID.
 *
 * @param int                  $drupal_id Drupal ID.
 * @param string|array<string> $post_type WordPress post type.
 * @param string               $meta_key  Meta key to search for Drupal ID.
 *
 * @return WP_Post|false
 */
function get_post_by_id( int $drupal_id = 0, string|array $post_type = 'post', string $meta_key = 'drupal_id' ): WP_Post|false {
	// Prepare arguments.
	$arguments = [
		'post_type'     => $post_type,
		'meta_key'      => $meta_key,
		'meta_value'    => $drupal_id,
		'post_status'   => 'any',
		'post_per_page' => 1,
	];

	// Query post.
	$posts = new WP_Query( $arguments );

	// If no post found then bail out.
	if ( empty( $posts->posts ) ) {
		return false;
	}

	// If not instance of WP_Post then bail out.
	if ( ! $posts->posts[0] instanceof WP_Post ) {
		return false;
	}

	// Return post.
	return $posts->posts[0];
}

/**
 * Prepare content for migration.
 *
 * @param string $content Original content.
 *
 * @return string
 */
function prepare_content( string $content = '' ): string {
	// Transform shortcodes.
	$content = iconv( 'UTF-8', 'ASCII//TRANSLIT', $content );
	$content = transform_drupal_media_tags( strval( $content ) );
	$content = transform_image_tags( $content );
	$content = remove_empty_paragraphs( $content );

	// Convert to blocks and return output.
	return convert_to_blocks( $content );
}

/**
 * Remove empty paragraphs from content.
 *
 * @param string $content Input string.
 *
 * @return string
 */
function remove_empty_paragraphs( string $content = '' ): string {
	// Remove empty <p> tags from the start and end of the content.
	$content = preg_replace( '/^(<p>(\s|&nbsp;| )*<\/p>)+/', '', $content );
	$content = preg_replace( '/(<p>(\s|&nbsp;| )*<\/p>)+$/', '', strval( $content ) );

	// return output.
	return strval( $content );
}

/**
 * Transform a drupal media tag into IMG tags.
 *  i.e. - <drupal-media data-entity-type="media" alt="alternate text"
 *  data-entity-uuid="b3a11cbc-53a9-419d-b9b5-2497ac0ba2ba" data-align="center"
 *  data-caption="caption text">.
 *
 * @param string $content Input string.
 *
 * @return string
 */
function transform_drupal_media_tags( string $content = '' ): string {
	// Look for shortcode pattern.
	// preg_match_all with that has <drupal-media> tag.
	preg_match_all( '#<drupal-media .*?data-entity-uuid="(.*?)".*?>#', $content, $matches );

	// If no matches found then bail out.
	if ( empty( $matches[0] ) || empty( $matches[1] ) ) {
		return $content;
	}

	// Traverse results.
	foreach ( $matches[1] as $key => $uuid ) {
		// Look for Drupal media tag.
		if ( empty( $matches[0][ $key ] ) ) {
			continue;
		}

		// get alt, align and caption.
		preg_match( '/alt="(.*?)"/', $matches[0][ $key ], $alt );
		preg_match( '/data-align="(.*?)"/', $matches[0][ $key ], $align );
		preg_match( '/data-caption="(.*?)"/', $matches[0][ $key ], $caption );

		// Initialize image HTML.
		$image_html = '';

		// Get Drupal media based on UUID.
		$drupal_db = get_database();
		$media     = (array) $drupal_db->get_row(
			strval(
				$drupal_db->prepare(
					'
					SELECT
						*
					FROM
						media
					WHERE
						media.`uuid` = %s
					LIMIT 1
					',
					[
						$uuid,
					]
				)
			),
			ARRAY_A
		);

		// If no media found then bail out.
		if ( ! is_array( $media ) || empty( $media['mid'] ) ) {
			continue;
		}

		// Get the image on WordPress.
		$image = download_file_by_mid( $media['mid'] );

		// If image found then build HTML.
		if ( is_integer( $image ) && ! empty( $image ) ) {
			// Build HTML tag and replace the shortcode.
			$src = wp_get_attachment_image_src( $image, 'large' );

			// If image found then build HTML.
			if ( ! empty( $src ) && is_array( $src ) ) {
				// Get alt text.
				if ( empty( $alt ) ) {
					$alt = trim( wp_strip_all_tags( strval( get_post_meta( $image, '_wp_attachment_image_alt', true ) ) ) );
				} else {
					$alt = $alt[1];
				}

				// Check if it's remote video.
				if ( 'remote_video' === $media['bundle'] ) {
					// Get the video URL.
					$video_url = $drupal_db->get_var(
						strval(
							$drupal_db->prepare(
								'
								SELECT
									field_media_video_embed_field_value
								FROM
									media__field_media_video_embed_field
								WHERE
									entity_id = %d
								LIMIT 1
								',
								$media['mid']
							)
						)
					);

					// If video URL found then build HTML.
					if ( ! empty( $video_url ) ) {
						$image_html = sprintf(
							'<img class="fancy-video" src="%s" id="%s" alt="%s" />',
							$video_url,
							$image,
							$alt
						);
					}
				} else {
					// Build image HTML.
					$image_html = sprintf(
						'<img class="%s wp-image-%d size-large" src="%s" alt="%s" width="%d" height="%d" />',
						$align[1] ?? 'alignnone',
						$image,
						$src[0],
						$alt,
						$src[1],
						$src[2]
					);

					// if caption is available then add it.
					if ( ! empty( $caption ) ) {
						$image_html = sprintf(
							'<figure class="wp-block-image %s">%s<figcaption>%s</figcaption></figure>',
							$align[1] ?? 'alignnone',
							$image_html,
							wp_strip_all_tags( html_entity_decode( $caption[1] ) ),
						);
					}
				}
			}

			// Replace the drupal-media tag.
			if ( ! empty( $image_html ) ) {
				$content = str_replace( $matches[0][ $key ], $image_html, $content );
			}
		} elseif ( $image instanceof WP_Error && 'QUARK_migration_media_download_failed' === $image->get_error_code() ) {
			$image_html = sprintf(
				'<figure class="wp-block-image %s"><img src="%s" alt=""/><figcaption>%s</figcaption></figure>',
				$align[1] ?? 'alignnone',
				strval( $image->get_error_data( 'QUARK_migration_media_download_failed' ) ),
				! empty( $caption[1] ) ? wp_strip_all_tags( html_entity_decode( $caption[1] ) ) : ''
			);
		}

		// Replace the closing tag in string.
		$content = str_replace( $matches[0][ $key ] . '</drupal-media>', $image_html, $content );
	}

	// Return output.
	return $content;
}

/**
 * Transform an IMG tag to have the correct paths.
 *
 * @param string $content Input string.
 *
 * @return string
 */
function transform_image_tags( string $content = '' ): string {
	// Look for shortcode pattern.
	preg_match_all( '#<img .*?src="(.*?)".*?>#', $content, $matches );

	// If no matches found then bail out.
	if ( empty( $matches[0] ) || empty( $matches[1] ) ) {
		return $content;
	}

	// Traverse results.
	foreach ( $matches[1] as $key => $image_src ) {
		// Look for IMG tag.
		if ( empty( $matches[0][ $key ] ) ) {
			continue;
		}

		// Parse image SRC.
		$parsed_src = wp_parse_url( $image_src );

		// Ignore external images and invalid paths.
		if (
			! is_array( $parsed_src )
			|| empty( $parsed_src['path'] )
			|| ! str_contains( $parsed_src['path'], '/sites/default/files/' )
			|| ( ! empty( $parsed_src['host'] ) && ! str_contains( 'quarkexpeditions', $parsed_src['host'] ) )
		) {
			continue;
		}

		// Get Drupal image based on file name.
		$image = download_file_by_url( $parsed_src['path'] );

		// If image not found in database, download orphan media using image src path.
		if ( 0 === $image ) {
			$image = get_media_by_url( $parsed_src['path'] );
		}

		// If image found then build HTML.
		if ( is_integer( $image ) && ! empty( $image ) ) {
			// Build HTML tag and replace the shortcode.
			$src = wp_get_attachment_image_src( $image, 'large' );

			// If image found then build HTML.
			if ( ! empty( $src ) && is_array( $src ) ) {
				$alt        = trim( wp_strip_all_tags( strval( get_post_meta( $image, '_wp_attachment_image_alt', true ) ) ) );
				$image_html = sprintf(
					'<img class="alignnone wp-image-%d size-large" src="%s" alt="%s" width="%d" height="%d" />',
					$image,
					$src[0],
					$alt,
					$src[1],
					$src[2]
				);

				// Replace the new IMG tag in string.
				$content = str_replace( $matches[0][ $key ], $image_html, $content );
			}
		}
	}

	// Return output.
	return $content;
}

/**
 * Prepare SEO data for migration.
 *
 * @param mixed $seo_meta_data SEO meta data from drupal.
 *
 * @return array{}|array<string, string>
 */
function prepare_seo_data( mixed $seo_meta_data = [] ): array {
	// SEO data.
	$seo_data = [];

	// Check if data is array.
	if ( is_array( $seo_meta_data ) ) {
		$search_for   = [
			'[node:title]',
			'→',
			'|',
			'[site:name]',
			'[current-page:page-number]',
			'[current-page:pager]',
		];
		$replace_with = [
			'%%title%%',
			'%%sep%%',
			'%%sep%%',
			'%%sitename%%',
			'%%page%%',
			'',
		];

		// Process seo meta title for WP SEO plugin.
		if ( ! empty( $seo_meta_data['title'] ) ) {
			$seo_data['_yoast_wpseo_title'] = str_replace(
				$search_for,
				$replace_with,
				trim( $seo_meta_data['title'] )
			);
		}

		// Process seo meta description for WP SEO plugin.
		if ( ! empty( $seo_meta_data['description'] ) ) {
			$seo_data['_yoast_wpseo_metadesc'] = str_replace(
				$search_for,
				$replace_with,
				trim( $seo_meta_data['description'] )
			);
		}

		// Process SEO robots tags for WP SEO plugin.
		if ( ! empty( $seo_meta_data['robots'] ) && is_string( $seo_meta_data['robots'] ) ) {
			// Convert to array.
			$robots = array_map( 'trim', explode( ',', $seo_meta_data['robots'] ) );

			// Process each robots tag, remove if it's not starts with "no".
			$robots = array_filter(
				$robots,
				function ( $tag ) {
					return str_starts_with( $tag, 'no' );
				}
			);

			// Set robots tags.
			$seo_data['_yoast_wpseo_meta-robots-adv'] = implode( ',', $robots );
		}
	}

	// Return SEO data.
	return $seo_data;
}

/**
 * Get the remote video URL.
 *
 * @param int $entity_id Drupal entity ID.
 *
 * @return string
 */
function get_remote_video_url( int $entity_id = 0 ): string {
	// validate entity ID.
	if ( empty( $entity_id ) ) {
		return '';
	}

	// Drupal database instance.
	$drupal_db = get_database();

	// Get the video URL.
	$video_url = $drupal_db->get_var(
		strval(
			$drupal_db->prepare(
				'
				SELECT
					field_media_video_embed_field_value
				FROM
					media__field_media_video_embed_field
				WHERE
					entity_id = %d AND deleted = 0
				LIMIT 1
				',
				$entity_id
			)
		)
	);

	// Return video URL.
	return strval( $video_url );
}

/**
 * Get the WordPress permalink based on Drupal link.
 *
 * @param string $drupal_link  Drupal link.
 * @param bool   $dynamic_link Dynamic post link instead of permalink.
 *
 * @return string
 */
function get_wp_permalink( string $drupal_link = '', bool $dynamic_link = true ): string {
	// Clean up the link.
	$drupal_link = str_replace( 'internal:', '', $drupal_link );
	$drupal_link = str_replace( 'entity:', '', $drupal_link );

	// Get the post.
	$post = get_wp_post_from_drupal_link( $drupal_link );

	// If post found then return the link.
	if ( $post instanceof WP_Post ) {
		if ( true === $dynamic_link ) {
			return home_url( 'post/' . $post->ID );
		} else {
			// Return the permalink.
			return strval( get_permalink( $post ) );
		}
	}

	// Return the Drupal link.
	return $drupal_link;
}

/**
 * Get a WordPress post based on Drupal link.
 *
 * @param string $drupal_link Drupal link.
 *
 * @return WP_Post|false
 */
function get_wp_post_from_drupal_link( string $drupal_link = '' ): WP_Post|false {
	// Determine meta key.
	if ( str_contains( $drupal_link, 'node/' ) ) {
		$meta_key = 'drupal_id';
	} else {
		$meta_key = 'drupal_tid';
	}

	// Look for ID.
	preg_match( '#(.*)/([0-9]+)#', $drupal_link, $matches );

	// If no matches found then bail out.
	if ( empty( $matches[2] ) ) {
		return false;
	}

	// all post types.
	$post_types = [
		'post',
		'page',
		'qrk_press_release',
		'qrk_adventure_option',
		'qrk_expedition',
		'qrk_agreement',
		'qrk_region',
		'qrk_ship',
		'qrk_staff_member',
		'qrk_cabin_category',
		'qrk_inclusion_set',
		'qrk_exclusion_set',
		'qrk_pre_post_trip',
		'qrk_itinerary',
		'qrk_landing_page',
		'qrk_offer',
		'qrk_ship_deck',
	];

	// Get a post which matches this meta key and ID.
	$post = get_post_by_id( absint( $matches[2] ), $post_types, $meta_key );

	// We didn't find a post - lets look in our redirections list instead.
	if ( ! $post instanceof WP_Post ) {
		global $wpdb;
		$fallback_redirect = $wpdb->get_row(
			$wpdb->prepare(
				"
				SELECT
					*
				FROM
					{$wpdb->prefix}redirection_items
				WHERE
					`url` = %s
				ORDER BY
					id DESC
				LIMIT 1
				",
				[
					'/' . ltrim( $drupal_link, '/' ),
				]
			),
			ARRAY_A
		);

		// If no redirect found then bail out.
		if ( empty( $fallback_redirect['action_data'] ) ) {
			return false;
		}

		// We found a redirect, attempt to get the post.
		$wp_link_post_id = url_to_postid( $fallback_redirect['action_data'] );

		// If no post id found then bail out.
		if ( empty( $wp_link_post_id ) ) {
			return false;
		}

		// Get the post.
		$post = get_post( $wp_link_post_id );

		// If post not found then bail out.
		if ( ! $post instanceof WP_Post ) {
			return false;
		}
	}

	// Return the post.
	return $post;
}
