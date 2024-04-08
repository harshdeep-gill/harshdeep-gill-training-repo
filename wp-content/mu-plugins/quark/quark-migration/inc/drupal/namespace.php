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
	$url = str_replace( '/sites/default/files/', 'public://', $url );

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
 * Get WordPress attachment ID by Drupal MID.
 *
 * @param int $drupal_mid Drupal MID.
 *
 * @return int WordPress's attachment ID on success otherwise 0.
 */
function get_wp_attachment_id_by_mid( int $drupal_mid = 0 ): int {
	// Global $wpdb instance.
	global $wpdb;

	// Check whether MID is provided or not.
	if ( empty( $drupal_mid ) ) {
		return 0;
	}

	// Get attachment ID.
	$attachment = $wpdb->get_row(
		$wpdb->prepare(
			"
				SELECT
					postmeta_mid.meta_value AS drupal_image_id,
					$wpdb->posts.ID AS id
				FROM
					$wpdb->posts
				INNER JOIN $wpdb->postmeta AS `postmeta_mid`
					ON $wpdb->posts.ID = postmeta_mid.post_id
				WHERE
					$wpdb->posts.post_type = 'attachment'
					AND $wpdb->posts.post_status = 'inherit'
					AND ( postmeta_mid.meta_key = 'drupal_mid' AND postmeta_mid.meta_value = %d )
				",
			[
				$drupal_mid,
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
 * Get WordPress attachment ID by Drupal FID.
 *
 * @param int $drupal_fid Drupal FID.
 *
 * @return int WordPress's attachment ID on success otherwise 0.
 */
function get_wp_attachment_id_by_fid( int $drupal_fid = 0 ): int {
	// Global $wpdb instance.
	global $wpdb;

	// Check whether FID is provided or not.
	if ( empty( $drupal_fid ) ) {
		return 0;
	}

	// Get attachment ID.
	$attachment = $wpdb->get_row(
		$wpdb->prepare(
			"
					SELECT
						postmeta_fid.meta_value AS drupal_image_id,
						$wpdb->posts.ID AS id
					FROM
						$wpdb->posts
					INNER JOIN $wpdb->postmeta AS `postmeta_fid`
						ON $wpdb->posts.ID = postmeta_fid.post_id
					WHERE
						$wpdb->posts.post_type = 'attachment'
						AND $wpdb->posts.post_status = 'inherit'
						AND ( postmeta_fid.meta_key = 'drupal_fid' AND postmeta_fid.meta_value = %d )
					",
			[
				$drupal_fid,
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

	// Get existing attachment ID if exists.
	$wp_attachment_id = get_wp_attachment_id_by_fid( drupal_fid: $drupal_fid );

	// If Attachment ID already exists then bail out.
	if ( ! empty( $wp_attachment_id ) ) {
		return $wp_attachment_id;
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
		$tmp = download_url( 'https://www.quarkexpeditions.com/sites/default/files/' . $file_name );

		// If Failed to download media file then bail out.
		if ( is_wp_error( $tmp ) || ! is_string( $tmp ) ) {
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
	if ( ! empty( $file_data['thumbnail__alt'] ) ) {
		$post_data['meta_input']['_wp_attachment_image_alt'] = trim( strval( $file_data['field_media_image_alt'] ) );
	}

	// Media Title.
	if ( ! empty( $file_data['thumbnail__title'] ) ) {
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
 * @param int    $drupal_id Drupal ID.
 * @param string $post_type WordPress post type.
 *
 * @return WP_Post|false
 */
function get_post_by_id( int $drupal_id = 0, string $post_type = 'post' ): WP_Post|false {
	// Prepare arguments.
	$arguments = [
		'post_type'     => $post_type,
		'meta_key'      => 'drupal_id',
		'meta_value'    => $drupal_id,
		'post_status'   => 'any',
		'post_per_page' => 1,
	];

	// Query post.
	$posts = new WP_Query( $arguments );

	// If WP_Error then bail out.
	if ( is_wp_error( $posts ) ) {
		return false;
	}

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
	$content = transform_drupal_media_tags( $content );
	$content = transform_image_tags( $content );

	// Convert to blocks and return output.
	return convert_to_blocks( $content );
}

/**
 * Transform a drupal media tag into IMG tags.
 *  i.e. - <drupal-media data-entity-type="media" alt="alternate text" data-entity-uuid="b3a11cbc-53a9-419d-b9b5-2497ac0ba2ba" data-align="center" data-caption="caption text">.
 *
 * @param string $string Input string.
 *
 * @return string
 */
function transform_drupal_media_tags( string $string = '' ): string {
	// Look for shortcode pattern.
	// preg_match_all with that has <drupal-media> tag.
	preg_match_all( '#<drupal-media .*?data-entity-uuid="(.*?)".*?>#', $string, $matches );

	// If no matches found then bail out.
	if ( empty( $matches[0] ) || empty( $matches[1] ) ) {
		return $string;
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

		// Get Drupal media based on UUID.
		$drupal_db = get_database();
		$media     = $drupal_db->get_row(
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
		if ( empty( $media['mid'] ) ) {
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
						$caption[1]
					);
				}

				// Replace the drupal-media tag.
				$string = str_replace( $matches[0][ $key ], $image_html, $string );
			}
		}
	}

	// Return output.
	return $string;
}

/**
 * Transform an IMG tag to have the correct paths.
 *
 * @param string $string Input string.
 *
 * @return string
 */
function transform_image_tags( string $string = '' ): string {
	// Look for shortcode pattern.
	preg_match_all( '#<img .*?src="(.*?)".*?>#', $string, $matches );

	// If no matches found then bail out.
	if ( empty( $matches[0] ) || empty( $matches[1] ) ) {
		return $string;
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
			|| ! str_contains( $parsed_src['path'], '/sites/default' )
			|| ( ! empty( $parsed_src['host'] ) && ! str_contains( 'quarkexpeditions', $parsed_src['host'] ) )
		) {
			continue;
		}

		// Get Drupal image based on file name.
		$image = download_file_by_url( $parsed_src['path'] );

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
				$string = str_replace( $matches[0][ $key ], $image_html, $string );
			}
		}
	}

	// Return output.
	return $string;
}
