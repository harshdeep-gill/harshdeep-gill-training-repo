<?php
/**
 * Migrate: Inclusion Exclusion Sets.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use cli\progress\Bar;
use WP_CLI;
use WP_Error;
use WP_Term;
use WP_CLI\ExitException;

use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\get_post_by_id;
use function Quark\Migration\Drupal\get_term_by_id;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\InclusionSets\POST_TYPE as INCLUSION_SET_POST_TYPE;
use const Quark\ExclusionSets\POST_TYPE as EXCLUSION_SET_POST_TYPE;
use const Quark\InclusionSets\INCLUSION_EXCLUSION_CATEGORY;

/**
 * Class Inclusion_Exclusion_Set.
 */
class Inclusion_Exclusion_Set {

	/**
	 * Migrate all Inclusion Exclusion Sets.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts(): void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch Ship Inclusion Exclusion Sets data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for "Inclusion Exclusion Set" post-type!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "Inclusion Exclusion Set" post-type', count( $data ) );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Start inserting posts.
		foreach ( $data as $item ) {
			// Insert post.
			$progress->tick();
			$this->insert_post( $item );
		}

		// Finish progress bar.
		$progress->finish();
	}

	/**
	 * Insert post by passing drupal data.
	 *
	 * @param array{}|array<string, int|string> $drupal_post Drupal post data.
	 *
	 * @return void
	 */
	public function insert_post( array $drupal_post = [] ): void {
		// Normalize drupal post data.
		$normalized_post = $this->normalize_drupal_post( $drupal_post );

		// Make sure data is normalized.
		if ( empty( $normalized_post ) ) {
			WP_CLI::warning( 'Unable to normalize drupal post data!' );

			// Bail out.
			return;
		}

		// Check post exist or not.
		$wp_post = get_post_by_id( $normalized_post['meta_input']['drupal_id'], $normalized_post['post_type'] );

		// Insert/update post.
		if ( ! empty( $wp_post ) ) {
			$normalized_post['ID'] = $wp_post->ID;
			$output                = wp_update_post( $normalized_post );
		} else {
			$output = wp_insert_post( $normalized_post );
		}

		// Check if post inserted/updated or not.
		if ( $output instanceof WP_Error ) {
			// Print error.
			WP_CLI::warning( 'Unable to insert/update post!' );
		}
	}

	/**
	 * Normalize drupal post data.
	 *
	 * @param array{}|array<string, int|string> $item Drupal post data.
	 *
	 * @return array{}|array{
	 *     post_type : string,
	 *     post_author : string,
	 *     post_title : string,
	 *     post_date : string,
	 *     post_date_gmt : string,
	 *     post_modified : string,
	 *     post_modified_gmt : string,
	 *     post_content : string,
	 *     post_status : string,
	 *     comment_status : string,
	 *     ping_status : string,
	 *     meta_input : array{
	 *         drupal_id : int,
	 *     }
	 * }
	 */
	public function normalize_drupal_post( array $item = [] ): array {
		// Bail out if empty.
		if ( empty( $item ) ) {
			return [];
		}

		// Normalize data.
		$nid         = ! empty( $item['nid'] ) ? absint( $item['nid'] ) : 0;
		$title       = '';
		$created_at  = gmdate( 'Y-m-d H:i:s' );
		$modified_at = gmdate( 'Y-m-d H:i:s' );
		$status      = 'draft';

		// Title.
		if ( is_string( $item['title'] ) && ! empty( $item['title'] ) ) {
			$title = strval( qrk_sanitize_attribute( trim( $item['title'] ) ) );
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
			'post_type'         => INCLUSION_SET_POST_TYPE,
			'post_author'       => '1',
			'post_title'        => $title,
			'post_date'         => $created_at,
			'post_date_gmt'     => $created_at,
			'post_modified'     => $modified_at,
			'post_modified_gmt' => $modified_at,
			'post_content'      => '',
			'post_status'       => $status,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
			'meta_input'        => [],
		];

		// Alternate title.
		$alternate_title = $item['alternate_title'] ?? '';

		// Check if alternate title is string and not empty.
		if ( is_string( $alternate_title ) && ! empty( $alternate_title ) ) {
			$data['meta_input']['display_title'] = strval( qrk_sanitize_attribute( trim( $alternate_title ) ) );
		}

		// Set inclusion exclusion set data.
		$inclusion_exclusion_set_data = $this->get_inclusion_exclusion_set_data( $nid );

		// Check if we have data.
		if ( ! empty( $inclusion_exclusion_set_data ) ) {
			$set_count = 0;

			// Loop through data.
			foreach ( $inclusion_exclusion_set_data as $set_item ) {
				$set_meta_key                        = sprintf( 'set_%d_item', $set_item['delta'] );
				$data['meta_input'][ $set_meta_key ] = strval( qrk_sanitize_attribute( $set_item['inclusion_exclusion_set_value'] ) );

				// Increment set count.
				++$set_count;
			}

			// Set - set count.
			$data['meta_input']['set'] = $set_count;
		}

		// Set ship_category term_id.
		if ( ! empty( $item['incl_excl_category'] ) ) {
			$term = get_term_by_id( absint( $item['incl_excl_category'] ), INCLUSION_EXCLUSION_CATEGORY );

			// Check if we have a valid term.
			if ( $term instanceof WP_Term ) {
				$data['tax_input'][ INCLUSION_EXCLUSION_CATEGORY ][] = $term->term_id;
			}
		}

		// Set drupal id metadata.
		$data['meta_input']['drupal_id'] = $nid;

		// Check if title contains 'Inclusion' or 'Exclusion'.
		if (
			false !== stripos( $title, 'Inclusion' )
			|| false !== stripos( $title, 'include' )
			|| false !== stripos( strval( $alternate_title ), 'Inclusion' )
			|| false !== stripos( strval( $alternate_title ), 'include' )
			|| false !== stripos( strval( $alternate_title ), 'Mandatory' )
		) {
			$data['post_type'] = INCLUSION_SET_POST_TYPE;
		} elseif (
			false !== stripos( $title, 'Exclusion' )
			|| false !== stripos( $title, 'exclude' )
			|| false !== stripos( strval( $alternate_title ), 'Exclusion' )
			|| false !== stripos( strval( $alternate_title ), 'exclude' )
			|| false !== stripos( strval( $alternate_title ), 'excl' )
		) {
			$data['post_type'] = EXCLUSION_SET_POST_TYPE;
		}

		// Return normalized data.
		return $data;
	}

	/**
	 * Fetch data from drupal database.
	 *
	 * @return array{}|array<int, array<string, int|string>> Drupal data.
	 *
	 * @throws ExitException Exit on failure to fetch data.
	 */
	public function get_drupal_data(): array {
		// Get database connection.
		$drupal_database = get_database();

		// Query.
		$query = "SELECT
			node.nid,
			field_data.status,
			field_data.title,
			field_data.created,
			field_data.changed,
			field_alternate_title.field_alternate_title_value AS alternate_title,
			field_incl_excl_category.field_incl_excl_category_target_id AS incl_excl_category
		FROM
			node
				LEFT JOIN node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
				LEFT JOIN node__field_alternate_title AS field_alternate_title ON node.nid = field_alternate_title.entity_id AND node.langcode = field_alternate_title.langcode
				LEFT JOIN node__field_incl_excl_category AS field_incl_excl_category ON node.nid = field_incl_excl_category.entity_id AND node.langcode = field_incl_excl_category.langcode
		WHERE
			node.type = 'inclusion_exclusion_set'
		order by incl_excl_category DESC";

		// Fetch data.
		$result = $drupal_database->get_results( $query, ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			WP_CLI::error( 'Unable to fetch data!' );

			// Bail out.
			return [];
		}

		// Return data.
		return $result;
	}

	/**
	 * Get public inclusion exclusion set data.
	 *
	 * @param int $nid Node ID.
	 *
	 * @return array{}|array<int, array<string, int|string>> Drupal data.
	 */
	public function get_inclusion_exclusion_set_data( int $nid = 0 ): array {
		// Get database connection.
		$drupal_database = get_database();

		// Query.
		$query = "SELECT
			entity_id,
			delta,
			field_inclusion_exclusion_set_value AS inclusion_exclusion_set_value
		FROM
			node__field_inclusion_exclusion_set AS field_inclusion_exclusion_set
		WHERE
			entity_id = %d AND field_inclusion_exclusion_set.langcode = 'en'
		ORDER BY
			delta ASC";

		// Fetch data.
		$result = $drupal_database->get_results( $drupal_database->prepare( $query, $nid ), ARRAY_A );

		// Check if data is not array.
		if ( ! is_array( $result ) ) {
			// Bail out.
			return [];
		}

		// Return data.
		return $result;
	}
}
