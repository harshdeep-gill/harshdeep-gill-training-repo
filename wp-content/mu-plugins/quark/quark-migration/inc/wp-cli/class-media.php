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
use function Quark\Migration\Drupal\get_wp_attachment_id;
use function WP_CLI\Utils\make_progress_bar;

/**
 * Class Media.
 */
class Media {

	/**
	 * Migrate media.
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
	public function all( array $args = [], array $args_assoc = [] ) : void {
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
			mfmf.field_media_file_description, mfic.field_image_caption_value
		FROM
			file_managed
			LEFT JOIN media_field_data as mfd ON mfd.thumbnail__target_id = file_managed.fid
			LEFT JOIN media__field_media_image as mfmi ON mid = mfmi.entity_id
			LEFT JOIN media__field_media_file as mfmf ON mfd.mid = mfmf.entity_id
			LEFT JOIN media__field_image_caption as mfic ON mfd.mid = mfic.entity_id
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
			if ( ! is_int( $wp_attachment_id ) && is_wp_error( $wp_attachment_id ) ) {
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

		// Update metadata for media files.
		$this->update_meta_data();
	}

	/**
	 * Update meta data.
	 *
	 * @return void
	 * @throws ExitException Exception on Error.
	 */
	public function update_meta_data() : void {
		// Update SVG meta data.
		$this->update_svg_meta_data();

		// Update branding term data for media.
		$this->update_branding_data();

		// Update destination term data for media.
		$this->update_destination_data();

		// Update photographer credit data for media.
		$this->update_photographer_credit_data();

		// Update season data for media.
		$this->update_season_data();
	}

	/**
	 * Update metadata for SVG.
	 *
	 * @return void
	 * @throws ExitException Exception on Error.
	 */
	public function update_svg_meta_data() : void {
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
	 * Update branding data for media.
	 *
	 * @return void
	 * @throws ExitException Exception on Error.
	 */
	public function update_branding_data() : void {
		// Update branding data for media.
		$this->process_meta_data( table_name: 'media__field_branding', field_name: 'field_branding_target_id', meta_key: 'branding_term_id' );
	}

	/**
	 * Update destination data for media.
	 *
	 * @return void
	 * @throws ExitException Exception on Error.
	 */
	public function update_destination_data() : void {
		// Update destination data for media.
		$this->process_meta_data( table_name: 'media__field_destinations', field_name: 'field_destinations_target_id', meta_key: 'destinations_term_id' );
	}

	/**
	 * Update photographer credit data for media.
	 *
	 * @return void
	 * @throws ExitException Exception on Error.
	 */
	public function update_photographer_credit_data() : void {
		// Update photographer credit data for media.
		$this->process_meta_data( table_name: 'media__field_photographer_credit', field_name: 'field_photographer_credit_value', meta_key: 'photographer_credit' );
	}

	/**
	 * Update season data for media.
	 *
	 * @return void
	 * @throws ExitException Exception on Error.
	 */
	public function update_season_data() : void {
		// Update season data for media.
		$this->process_meta_data( table_name: 'media__field_season', field_name: 'field_season_value', meta_key: 'season_term_id' );
	}

	/**
	 * Update meta data.
	 *
	 * @param string $table_name Table name.
	 * @param string $field_name Field name.
	 * @param string $meta_key   Meta key.
	 *
	 * @return void
	 * @throws ExitException Exception on Error.
	 */
	public function process_meta_data( string $table_name = '', string $field_name = '', string $meta_key = '' ) : void {
		// validate table name, field name and meta key.
		if ( empty( $table_name ) || empty( $field_name ) || empty( $meta_key ) ) {
			WP_CLI::error( 'Table name, field name and meta key are required!' );
		}

		// Build query.
		$drupal_db = get_database();
		$query     = "SELECT * FROM $table_name";

		// Get metadata from drupal.
		$metadata = (array) $drupal_db->get_results( $query, ARRAY_A );

		// If there is no record then bail out.
		if ( empty( $metadata ) ) {
			WP_CLI::error( 'No data found!' );
		}

		// Progress bar.
		$total_records = count( $metadata );
		$progress      = make_progress_bar( 'Migrating', $total_records );
		$count         = 0;
		WP_CLI::log( WP_CLI::colorize( '%GFound metadata: %n' . $total_records ) );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Migrate images.
		foreach ( $metadata as $meta ) {
			$media_id   = absint( $meta['entity_id'] );
			$meta_value = absint( $meta[ $field_name ] );

			// If there is no meta value then bail out.
			if ( ! empty( $meta_value ) ) {
				// Get attachment ID.
				$attachment_id = get_wp_attachment_id( drupal_mid: $media_id );

				// If there is no attachment ID then bail out.
				if ( $attachment_id ) {
					// Update attachment meta data.
					update_post_meta( $attachment_id, $meta_key, $meta_value );
					++$count;
				}
			}

			// Update progress.
			$progress->tick();
		}

		// All done!
		$progress->finish();
		WP_CLI::success( "Migrated metadata $count out of $total_records media." );
	}
}
