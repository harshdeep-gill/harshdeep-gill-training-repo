<?php
/**
 * Migrate: Media.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use WP_CLI;
use WP_CLI\ExitException;
use WP_Error;
use WP_Post;
use cli\progress\Bar;

use function Quark\Core\update_svg_content;
use function Quark\Migration\Drupal\download_file;
use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\download_file_by_mid;
use function Quark\Migration\Drupal\download_file_by_fid;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\Brochures\POST_TYPE;

/**
 * Class Media.
 */
class Media {

	/**
	 * Migrate media.
	 *
	 * ## OPTIONS
	 *
	 * [--ids=<1,2>]
	 * : Comma separated list of media IDs.
	 *
	 * [--from-id=<number>]
	 * : Media ID from where to start migration.
	 *
	 * [--chunk=<number>]
	 * : Chunk number to migrate.
	 *
	 * [--total-chunks=<number>]
	 * : Total number of items per chunk.
	 *
	 * ## EXAMPLES
	 *    wp quark-migrate media all
	 *    wp quark-migrate media all --ids=1,2,3
	 *    wp quark-migrate media all --from-id=1
	 *    wp quark-migrate media all --chunk=1 --total-chunks=50
	 *
	 * @param mixed[] $args       WP CLI arguments.
	 * @param mixed[] $args_assoc WP CLI associative arguments.
	 *
	 * @subcommand all
	 * @synopsis [--ids=<1,2>] [--from-id=<number>] [--chunk=<number>] [--total-chunks=<number>]
	 *
	 * @return void
	 * @throws ExitException Exception on error.
	 */
	public function all( array $args = [], array $args_assoc = [] ): void {
		// Get options.
		$options = wp_parse_args(
			$args_assoc,
			[
				'ids'          => [],
				'from-id'      => 0,
				'chunk'        => 0,
				'total-chunks' => 5,
			]
		);

		// Sanitize the value of ids option.
		if ( ! empty( $options['ids'] ) ) {
			$options['ids'] = array_map( 'absint', array_filter( array_map( 'trim', explode( ',', $options['ids'] ) ) ) );
		} else {
			$options['ids'] = [];
		}

		// Sanitize the value of from-id option.
		if ( ! empty( $options['from-id'] ) ) {
			$options['from-id'] = absint( $options['from-id'] );
		}

		// Sanitize the value of chunk option.
		if ( ! empty( $args_assoc['chunk'] ) ) {
			$options['chunk'] = absint( $options['chunk'] );
		}

		// Sanitize the value of total-chunks option.
		if ( ! empty( $options['total-chunks'] ) ) {
			$options['total-chunks'] = absint( $options['total-chunks'] );
		}

		// Welcome message.
		WP_CLI::log( WP_CLI::colorize( '%YMigrating images from Drupal...%n' ) );

		// Build query.
		$drupal_db = get_database();
		$query     = '
		SELECT
			fid, filename,filemime, uri, origname,
			mid, mfd.bundle as "bundle", name, thumbnail__target_id,
			field_media_image_alt, field_media_image_title,
			mfmf.field_media_file_description, mfic.field_image_caption_value,
			mfpc.field_photographer_credit_value
		FROM
			file_managed
			LEFT JOIN media_field_data as mfd ON mfd.thumbnail__target_id = file_managed.fid
			LEFT JOIN media__field_media_image as mfmi ON mid = mfmi.entity_id
			LEFT JOIN media__field_media_file as mfmf ON mfd.mid = mfmf.entity_id
			LEFT JOIN media__field_image_caption as mfic ON mfd.mid = mfic.entity_id
			LEFT JOIN media__field_photographer_credit as mfpc ON mfd.mid = mfpc.entity_id
		WHERE
			1=1
		';

		// Update query when ids are provided.
		if ( ! empty( $options['ids'] ) ) {
			$query .= ' AND fid IN (' . implode( ',', $options['ids'] ) . ')';
		}

		// Update query when from-id is provided.
		if ( ! empty( $options['from-id'] ) ) {
			$query .= ' AND fid >= ' . $options['from-id'];
		}

		// Add ORDER BY in SQL query.
		$query .= ' ORDER BY fid ASC';

		// If values of chunk is provided then update SQL query.
		if ( ! empty( $options['chunk'] ) ) {
			$total_images = $drupal_db->get_row(
				'
				SELECT
					COUNT(*) AS total_images
				FROM
					file_managed
					LEFT JOIN media_field_data as mfd ON mfd.thumbnail__target_id = file_managed.fid
				',
				ARRAY_A
			);
			$total_images = absint( $total_images['total_images'] ?? 0 );

			// Check if we have media record or not.
			if ( $total_images > 0 ) {
				$rows_per_chunk = ceil( $total_images / $options['total-chunks'] );
				$offset         = ( $options['chunk'] - 1 ) * $rows_per_chunk;

				// Add Limit in SQL query.
				$query .= " LIMIT $rows_per_chunk OFFSET $offset";
			}
		}

		// Get images.
		$images = (array) $drupal_db->get_results( $query, ARRAY_A );

		// If there is no record then bail out.
		if ( empty( $images ) ) {
			WP_CLI::error( 'No images found!' );
		}

		// Progress bar.
		$total_images = count( $images );
		$progress     = make_progress_bar( 'Migrating', $total_images );
		$count        = 0;
		WP_CLI::log( WP_CLI::colorize( '%GFound media: %n' . $total_images ) );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Prepare for migration.
		prepare_for_migration();

		// Migrate images.
		foreach ( $images as $image ) {
			// Download image.
			$wp_attachment_id = download_file( $image );

			// If there is any error then show warning.
			if ( $wp_attachment_id instanceof WP_Error ) {
				WP_CLI::warning(
					sprintf(
						'Error while migrating image: %s - %s',
						$wp_attachment_id->get_error_message(),
						$image['uri']
					)
				);
			}

			// Update progress.
			++$count;
			$progress->tick();
		}

		// All done!
		$progress->finish();
		WP_CLI::success( "Migrated $count out of $total_images media." );

		// Update SVG meta data.
		$this->update_svg_meta_data();
	}

	/**
	 * Update metadata for SVG.
	 *
	 * @return void
	 * @throws ExitException Exception on Error.
	 */
	public function update_svg_meta_data(): void {
		// Get all SVG attachments.
		$attachments = get_posts(
			[
				'post_type'      => 'attachment',
				'post_mime_type' => 'image/svg+xml',
				'posts_per_page' => -1,
			]
		);

		// If there is no record then bail out.
		if ( empty( $attachments ) || ! is_array( $attachments ) ) {
			WP_CLI::error( 'No SVG found!', false );
		}

		// Progress bar.
		$total_images = count( $attachments );
		$progress     = make_progress_bar( 'Updating', $total_images );
		$count        = 0;
		WP_CLI::log( WP_CLI::colorize( '%GFound SVG: %n' . $total_images ) );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Update SVG content.
		foreach ( $attachments as $attachment ) {
			// If attachment is not instance of WP_Post then bail out.
			if ( ! $attachment instanceof WP_Post ) {
				continue;
			}

			// Update SVG content.
			update_svg_content( $attachment->ID );

			// Update progress.
			++$count;
			$progress->tick();
		}

		// All done!
		$progress->finish();
		WP_CLI::success( "Updated $count out of $total_images SVG." );
	}

	/**
	 * Migrate pdf files.
	 *
	 * ## EXAMPLES
	 *    wp quark-migrate media pdf_files
	 *
	 * @subcommand pdf_files
	 *
	 * @return void
	 * @throws ExitException Exception on error.
	 */
	public function pdf_files(): void {
		// Welcome message.
		WP_CLI::log( WP_CLI::colorize( '%YMigrating pdf files from Drupal...%n' ) );

		// Build query.
		$drupal_db = get_database();
		$query     = '
		SELECT
			mid, mfd.bundle as "bundle", name, created, changed, status,
			field_pdf_image_target_id,
			field_pdf_is_gated_value,
			field_url_uri,
			field_url_title,
			field_season_value,
			field_media_file_target_id
		FROM
			media_field_data as mfd
				LEFT JOIN media__field_pdf_image as mfpi ON mfd.mid = mfpi.entity_id
				LEFT JOIN media__field_media_file as mfmf ON mfd.mid = mfmf.entity_id
				LEFT JOIN media__field_pdf_is_gated as mfpg ON mfd.mid = mfpg.entity_id
				LEFT JOIN media__field_url as mfurl ON mfd.mid = mfurl.entity_id
				LEFT JOIN media__field_season as mfs ON mfd.mid = mfs.entity_id
		WHERE
			mfd.bundle in ( "brochure", "dossier", "pre_departure_book", "price_list", "destination_guide", "ship_book" )
		';

		// Get pdf nodes.
		$pdfs = (array) $drupal_db->get_results( $query, ARRAY_A );

		// If there is no record then bail out.
		if ( empty( $pdfs ) ) {
			WP_CLI::error( 'No pdf nodes found!' );
		}

		// Progress bar.
		$total_pdf = count( $pdfs );
		$progress  = make_progress_bar( 'Migrating', $total_pdf );
		$count     = 0;
		WP_CLI::log( WP_CLI::colorize( '%GFound media: %n' . $total_pdf ) );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Prepare for migration.
		prepare_for_migration();

		// Migrate pdfs.
		foreach ( $pdfs as $pdf ) {
			// Normalize drupal post data.
			$normalized_post = $this->normalize_drupal_post( $pdf );

			// Make sure data is normalized.
			if ( empty( $normalized_post ) ) {
				WP_CLI::warning( 'Unable to normalize drupal post data!' );

				// Bail out.
				return;
			}

			// Insert post.
			$output = wp_insert_post( $normalized_post );

			// Check if post inserted/updated or not.
			if ( $output instanceof WP_Error ) {
				// Print error.
				WP_CLI::warning( sprintf( 'Unable to insert/update Brochure post: %d', $pdf['mid'] ) );
			} else {
				++$count;
			}

			// Update progress.
			$progress->tick();
		}

		// All done!
		$progress->finish();
		WP_CLI::success( "Migrated $count out of $total_pdf pdf files." );
	}

	/**
	 * Normalize drupal post data.
	 *
	 * @param array{}|array<string, int|string> $item Drupal post data.
	 *
	 * @return array{}|array{
	 *     post_type: string,
	 *     post_author: string,
	 *     post_title : string,
	 *     post_date : string,
	 *     post_date_gmt : string,
	 *     post_modified : string,
	 *     post_modified_gmt : string,
	 *     post_status : string,
	 *     comment_status: string,
	 *     ping_status: string,
	 *     meta_input : array{
	 *          drupal_mid : int,
	 *          bundle : string,
	 *     }
	 * }
	 */
	public function normalize_drupal_post( array $item = [] ): array {
		// Bail out if empty.
		if ( empty( $item ) ) {
			return [];
		}

		// Normalize data.
		$mid         = ! empty( $item['mid'] ) ? absint( $item['mid'] ) : 0;
		$title       = '';
		$created_at  = gmdate( 'Y-m-d H:i:s' );
		$modified_at = gmdate( 'Y-m-d H:i:s' );
		$status      = 'draft';

		// Title.
		if ( is_string( $item['name'] ) && ! empty( $item['name'] ) ) {
			$title = trim( $item['name'] );
		}

		// Created date.
		if ( ! empty( $item['created'] ) ) {
			$created_at = gmdate( 'Y-m-d H:i:s', absint( $item['created'] ) );
		}

		// Modified date.
		if ( ! empty( $item['changed'] ) ) {
			$modified_at = gmdate( 'Y-m-d H:i:s', absint( $item['changed'] ) );
		}

		// Status.
		if ( ! empty( $item['status'] ) && 1 === absint( $item['status'] ) ) {
			$status = 'publish';
		}

		// Prepare post data.
		$data = [
			'post_type'         => POST_TYPE,
			'post_author'       => '1',
			'post_title'        => strval( qrk_sanitize_attribute( $title ) ),
			'post_date'         => $created_at,
			'post_date_gmt'     => $created_at,
			'post_modified'     => $modified_at,
			'post_modified_gmt' => $modified_at,
			'post_status'       => $status,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
			'meta_input'        => [
				'drupal_mid' => $mid,
				'bundle'     => strval( $item['bundle'] ),
			],
		];

		// Get Featured image.
		if ( ! empty( $item['field_pdf_image_target_id'] ) ) {
			$wp_thumbnail_id = download_file_by_mid( absint( $item['field_pdf_image_target_id'] ) );

			// Set featured image.
			if ( ! empty( $wp_thumbnail_id ) && is_int( $wp_thumbnail_id ) ) {
				$data['meta_input']['_thumbnail_id'] = $wp_thumbnail_id;
			}
		}

		// Get PDF file ID.
		if ( ! empty( $item['field_media_file_target_id'] ) ) {
			$wp_pdf_id = download_file_by_fid( absint( $item['field_media_file_target_id'] ) );

			// Set PDF file.
			if ( ! empty( $wp_pdf_id ) && is_int( $wp_pdf_id ) ) {
				$data['meta_input']['pdf_file'] = $wp_pdf_id;
			}
		}

		// Get PDF file is_gated meta.
		if ( ! empty( $item['field_pdf_is_gated_value'] ) ) {
			$data['meta_input']['pdf_is_gated'] = boolval( $item['field_pdf_is_gated_value'] );
		}

		// Get PDF file external URL.
		if ( ! empty( $item['field_url_uri'] ) ) {
			$data['meta_input']['external_url'] = strval( $item['field_url_uri'] );
		}

		// Get PDF file season data.
		if ( ! empty( $item['field_season_value'] ) ) {
			$data['meta_input']['season'] = strval( $item['field_season_value'] );
		}

		// Return normalized data.
		return $data;
	}
}
