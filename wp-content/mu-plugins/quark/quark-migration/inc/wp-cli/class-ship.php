<?php
/**
 * Migrate: Ship nodes from Drupal to WordPress CPT.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use cli\progress\Bar;
use WP_CLI;
use WP_Error;
use WP_CLI\ExitException;
use WP_Term;

use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\get_term_by_id;
use function Quark\Migration\Drupal\prepare_content;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\get_post_by_id;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\Ships\POST_TYPE;
use const Quark\Ships\SHIP_CATEGORY_TAXONOMY;

/**
 * Class Ship.
 */
class Ship {

	/**
	 * Migrate all Ships.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts(): void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch ships data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for ship!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "Ship" post-type', count( $data ) );

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
		$wp_post = get_post_by_id( $normalized_post['meta_input']['drupal_id'], POST_TYPE );

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
			WP_CLI::warning( 'Unable to insert/update ship - ' . $normalized_post['meta_input']['drupal_id'] );
		}
	}

	/**
	 * Normalize drupal post data.
	 *
	 * @param array{}|array<string, int|string> $item Drupal post data.
	 *
	 * @return array{}|array{
	 *     post_type: string,
	 *     post_author: string,
	 *     post_title: string,
	 *     post_date: string,
	 *     post_date_gmt: string,
	 *     post_modified: string,
	 *     post_modified_gmt: string,
	 *     post_name: string,
	 *     post_content: string,
	 *     post_excerpt: string,
	 *     post_status: string,
	 *     comment_status: string,
	 *     ping_status: string,
	 *     meta_input: array{
	 *          drupal_id : int,
	 *     }
	 * }
	 */
	public function normalize_drupal_post( array $item = [] ): array {
		// Bail out if empty.
		if ( empty( $item ) ) {
			return [];
		}

		// Normalize data.
		$nid          = ! empty( $item['nid'] ) ? absint( $item['nid'] ) : 0;
		$title        = '';
		$created_at   = gmdate( 'Y-m-d H:i:s' );
		$modified_at  = gmdate( 'Y-m-d H:i:s' );
		$status       = 'draft';
		$post_content = '';
		$post_excerpt = '';
		$post_name    = '';

		// Title.
		if ( is_string( $item['title'] ) && ! empty( $item['title'] ) ) {
			$title = trim( $item['title'] );
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

		// post content.
		if ( ! empty( $item['post_content'] ) ) {
			$post_content = strval( $item['post_content'] );
		}

		// post excerpt.
		if ( ! empty( $item['post_excerpt'] ) && is_string( $item['post_excerpt'] ) ) {
			$post_excerpt = wp_strip_all_tags( trim( $item['post_excerpt'] ) );
		}

		// Post name.
		if ( ! empty( $item['drupal_url'] ) && is_string( $item['drupal_url'] ) ) {
			/**
			 * Break the url into parts and use the last part as post name.
			 * i.e. - /expedition-ships/sea-spirit.
			 */
			$parts     = explode( '/', $item['drupal_url'] );
			$post_name = end( $parts );
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
			'post_name'         => $post_name,
			'post_content'      => prepare_content( strval( $post_content ) ),
			'post_excerpt'      => $post_excerpt,
			'post_status'       => $status,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
			'meta_input'        => [
				'drupal_id' => $nid,
			],
		];

		// Set ship_category term_id.
		if ( ! empty( $item['ship_category_term_id'] ) ) {
			$term = get_term_by_id( absint( $item['ship_category_term_id'] ), SHIP_CATEGORY_TAXONOMY );

			// Check if we have a valid term.
			if ( $term instanceof WP_Term ) {
				$data['tax_input'][ SHIP_CATEGORY_TAXONOMY ][] = $term->term_id;
			}
		}

		// Set Ship ID.
		if ( ! empty( $item['ship_id'] ) ) {
			$data['meta_input']['ship_id'] = strval( $item['ship_id'] );
		}

		// Set ship specifications - Breadth.
		if ( ! empty( $item['breadth'] ) ) {
			$data['meta_input']['breadth'] = strval( $item['breadth'] );
		}

		// Set ship specifications - Cruising Speed.
		if ( ! empty( $item['cruising_speed'] ) ) {
			$data['meta_input']['cruising_speed'] = strval( $item['cruising_speed'] );
		}

		// Set ship specifications - Gross Tonnage.
		if ( ! empty( $item['gross_tonnage'] ) ) {
			$data['meta_input']['gross_tonnage'] = strval( $item['gross_tonnage'] );
		}

		// Set ship specifications - Guest Ratio.
		if ( ! empty( $item['guest_ratio'] ) ) {
			$data['meta_input']['guest_ratio'] = strval( $item['guest_ratio'] );
		}

		// Set ship specifications - Guests.
		if ( ! empty( $item['guests'] ) ) {
			$data['meta_input']['guests'] = strval( $item['guests'] );
		}

		// Set ship specifications - Ice Class.
		if ( ! empty( $item['ice_class'] ) ) {
			$data['meta_input']['ice_class'] = strval( $item['ice_class'] );
		}

		// Set ship specifications - Year Refurbished.
		if ( ! empty( $item['year_refurbished'] ) ) {
			$data['meta_input']['year_refurbished'] = strval( $item['year_refurbished'] );
		}

		// Set ship specifications - Length.
		if ( ! empty( $item['length'] ) ) {
			$data['meta_input']['length'] = strval( $item['length'] );
		}

		// Set ship specifications - Lifeboats.
		if ( ! empty( $item['lifeboats'] ) ) {
			$data['meta_input']['lifeboats'] = strval( $item['lifeboats'] );
		}

		// Set ship specifications - Propulsion.
		if ( ! empty( $item['propulsion'] ) ) {
			$data['meta_input']['propulsion'] = strval( $item['propulsion'] );
		}

		// Set ship specifications - Registration.
		if ( ! empty( $item['registration'] ) ) {
			$data['meta_input']['registration'] = strval( $item['registration'] );
		}

		// Set ship specifications - Stabilizers.
		if ( ! empty( $item['stabilizers'] ) ) {
			$data['meta_input']['stabilizers'] = strval( $item['stabilizers'] );
		}

		// Set ship specifications - Staff and Crew.
		if ( ! empty( $item['staff_and_crew'] ) ) {
			$data['meta_input']['staff_and_crew'] = strval( $item['staff_and_crew'] );
		}

		// Set ship specifications - Voltage.
		if ( ! empty( $item['voltage'] ) ) {
			$data['meta_input']['voltage'] = strval( $item['voltage'] );
		}

		// Set ship specifications - Year Built.
		if ( ! empty( $item['year_built'] ) ) {
			$data['meta_input']['year_built'] = strval( $item['year_built'] );
		}

		// Set ship specifications - Zodiacs.
		if ( ! empty( $item['zodiacs'] ) ) {
			$data['meta_input']['zodiacs'] = strval( $item['zodiacs'] );
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
			field_data.publish_on,
			field_data.unpublish_on,
			( SELECT alias AS drupal_url FROM path_alias WHERE path = CONCAT( '/node/', node.nid ) ORDER BY id DESC LIMIT 0, 1 ) AS drupal_url,
			body.body_value AS post_content,
			body.body_summary AS post_excerpt,
			field_metatags.field_metatags_value AS metatags,
			field_ship_category.field_ship_category_target_id AS ship_category_term_id,
			field_ship_id.field_ship_id_value AS ship_id,
			field_breadth.field_breadth_value AS breadth,
			field_cruising_speed.field_cruising_speed_value AS cruising_speed,
			field_gross_tonnage.field_gross_tonnage_value AS gross_tonnage,
			field_guest_ratio.field_guest_ratio_value AS guest_ratio,
			field_guests.field_guests_value AS guests,
			field_ice_class.field_ice_class_value AS ice_class,
			field_year_refurbished.field_year_refurbished_value AS year_refurbished,
			field_length.field_length_value AS length,
			field_lifeboats.field_lifeboats_value AS lifeboats,
			field_propulsion.field_propulsion_value AS propulsion,
			field_registration.field_registration_value AS registration,
			field_stabilizers.field_stabilizers_value AS stabilizers,
			field_staff_and_crew.field_staff_and_crew_value AS staff_and_crew,
			field_voltage.field_voltage_value AS voltage,
			field_year_built.field_year_built_value AS year_built,
			field_zodiacs.field_zodiacs_value AS zodiacs
		FROM
			node
				LEFT JOIN node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
				LEFT JOIN `node__body` AS `body` ON node.nid = body.entity_id AND node.langcode = body.langcode
				LEFT JOIN `node__field_metatags` AS `field_metatags` ON node.nid = field_metatags.entity_id AND node.langcode = field_metatags.langcode
				LEFT JOIN `node__field_ship_category` AS `field_ship_category` ON node.nid = field_ship_category.entity_id AND node.langcode = field_ship_category.langcode
				LEFT JOIN `node__field_ship_id` AS `field_ship_id` ON node.nid = field_ship_id.entity_id AND node.langcode = field_ship_id.langcode
				LEFT JOIN `node__field_ship_specifications` AS `field_ship_specifications` ON node.nid = field_ship_specifications.entity_id AND node.langcode = field_ship_specifications.langcode
				LEFT JOIN paragraph__field_breadth AS field_breadth ON field_ship_specifications.field_ship_specifications_target_id = field_breadth.entity_id
				LEFT JOIN paragraph__field_cruising_speed AS field_cruising_speed ON field_ship_specifications.field_ship_specifications_target_id = field_cruising_speed.entity_id
				LEFT JOIN paragraph__field_draft AS field_draft ON field_ship_specifications.field_ship_specifications_target_id = field_draft.entity_id
				LEFT JOIN paragraph__field_gross_tonnage AS field_gross_tonnage ON field_ship_specifications.field_ship_specifications_target_id = field_gross_tonnage.entity_id
				LEFT JOIN paragraph__field_guest_ratio AS field_guest_ratio ON field_ship_specifications.field_ship_specifications_target_id = field_guest_ratio.entity_id
				LEFT JOIN paragraph__field_guests AS field_guests ON field_ship_specifications.field_ship_specifications_target_id = field_guests.entity_id
				LEFT JOIN paragraph__field_ice_class AS field_ice_class ON field_ship_specifications.field_ship_specifications_target_id = field_ice_class.entity_id
				LEFT JOIN paragraph__field_year_refurbished AS field_year_refurbished ON field_ship_specifications.field_ship_specifications_target_id = field_year_refurbished.entity_id
				LEFT JOIN paragraph__field_length AS field_length ON field_ship_specifications.field_ship_specifications_target_id = field_length.entity_id
				LEFT JOIN paragraph__field_lifeboats AS field_lifeboats ON field_ship_specifications.field_ship_specifications_target_id = field_lifeboats.entity_id
				LEFT JOIN paragraph__field_propulsion AS field_propulsion ON field_ship_specifications.field_ship_specifications_target_id = field_propulsion.entity_id
				LEFT JOIN paragraph__field_registration AS field_registration ON field_ship_specifications.field_ship_specifications_target_id = field_registration.entity_id
				LEFT JOIN paragraph__field_stabilizers AS field_stabilizers ON field_ship_specifications.field_ship_specifications_target_id = field_stabilizers.entity_id
				LEFT JOIN paragraph__field_staff_and_crew AS field_staff_and_crew ON field_ship_specifications.field_ship_specifications_target_id = field_staff_and_crew.entity_id
				LEFT JOIN paragraph__field_voltage AS field_voltage ON field_ship_specifications.field_ship_specifications_target_id = field_voltage.entity_id
				LEFT JOIN paragraph__field_year_built AS field_year_built ON field_ship_specifications.field_ship_specifications_target_id = field_year_built.entity_id
				LEFT JOIN paragraph__field_zodiacs AS field_zodiacs ON field_ship_specifications.field_ship_specifications_target_id = field_zodiacs.entity_id
		WHERE
			node.type = 'ship';";

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
}
