<?php
/**
 * Migrate: Media.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use WP_CLI;
use WP_CLI\ExitException;
use WP_Post;
use cli\progress\Bar;

use function Quark\Core\update_svg_content;
use function Quark\Migration\Drupal\download_file;
use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\prepare_for_migration;
use function WP_CLI\Utils\make_progress_bar;

/**
 * Class Media.
 */
class Media {

	/**
	 * Migrate media.
	 *
	 * @param mixed[] $args WP CLI arguments.
	 * @param mixed[] $args_assoc WP CLI associative arguments.
	 *
	 * @subcommand all
	 * @synopsis [--ids=<1,2>] [--from-id=<number>] [--chunk=<number>] [--total-chunks=<number>]
	 *
	 * @throws ExitException Exception on error.
	 *
	 * @return void
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
			file_managed.*,
			media_field_data.*
		FROM
			file_managed
			LEFT JOIN media_field_data ON media_field_data.thumbnail__target_id = file_managed.fid
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
					LEFT JOIN media_field_data ON media_field_data.thumbnail__target_id = file_managed.fid
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

		// If there is not record then bail out.
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

			// Update progress.
			++$count;
			$progress->tick();
		}

		// All done!
		$progress->finish();
		WP_CLI::success( "Migrated $count out of $total_images media." );

		// Update metadata for SVG files.
		$this->update_meta_data();
	}

	/**
	 * Update metadata for SVG.
	 *
	 * @throws ExitException Exception on Error.
	 *
	 * @return void
	 */
	public function update_meta_data(): void {
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
			WP_CLI::error( 'No SVG found!' );
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
}
