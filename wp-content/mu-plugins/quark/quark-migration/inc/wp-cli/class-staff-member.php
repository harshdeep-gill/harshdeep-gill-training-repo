<?php
/**
 * Migrate: Staff Members nodes from Drupal to WordPress CPT.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use cli\progress\Bar;
use WP_CLI;
use WP_Error;
use WP_CLI\ExitException;
use WP_Term;
use WP_Post;

use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\get_term_by_id;
use function Quark\Migration\Drupal\download_file_by_mid;
use function Quark\Migration\Drupal\prepare_content;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\get_post_by_id;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function Quark\Migration\Drupal\prepare_seo_data;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\StaffMembers\POST_TYPE;
use const Quark\Regions\POST_TYPE as REGION_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;
use const Quark\StaffMembers\SEASON_TAXONOMY;
use const Quark\StaffMembers\DEPARTURE_STAFF_ROLE_TAXONOMY;
use const Quark\StaffMembers\DEPARTMENT_TAXONOMY;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;

/**
 * Class Staff_Member.
 */
class Staff_Member {

	/**
	 * Migrate all Staff Member.
	 *
	 * @subcommand all
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function posts(): void {
		// Prepare for migration.
		prepare_for_migration();

		// Fetch Staff Members data from drupal database.
		$data = $this->get_drupal_data();

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( 'Unable to fetch data for Staff Member!' );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( 'Total Found: ' . count( $data ) );

		// Initialize progress bar.
		$progress = make_progress_bar( 'Migrating "Staff Member" post-type', count( $data ) );

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
			WP_CLI::warning( 'Unable to insert/update Staff Member - ' . $normalized_post['meta_input']['drupal_id'] );
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
			 * i.e. - /staff/david-woody-wood
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
			'post_content'      => prepare_content( $post_content ),
			'post_excerpt'      => $post_excerpt,
			'post_status'       => $status,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
			'meta_input'        => [],
		];

		// SEO meta data.
		if ( ! empty( $item['metatags'] ) && is_string( $item['metatags'] ) ) {
			$seo_data = prepare_seo_data( json_decode( $item['metatags'], true ) );

			// Merge seo data if not empty.
			if ( ! empty( $seo_data ) ) {
				$data['meta_input'] = array_merge( $seo_data, $data['meta_input'] );
			}
		}

		// Set fallback as excerpt if meta description is empty.
		if ( empty( $data['meta_input']['_yoast_wpseo_metadesc'] ) ) {
			$data['meta_input']['_yoast_wpseo_metadesc'] = $data['post_excerpt'];
		}

		// Set first_name.
		if ( ! empty( $item['first_name'] ) && is_string( $item['first_name'] ) ) {
			$data['meta_input']['first_name'] = qrk_sanitize_attribute( $item['first_name'] );
		}

		// Set last_name.
		if ( ! empty( $item['last_name'] ) && is_string( $item['last_name'] ) ) {
			$data['meta_input']['last_name'] = qrk_sanitize_attribute( $item['last_name'] );
		}

		// Set hometown.
		if ( ! empty( $item['hometown'] ) && is_string( $item['hometown'] ) ) {
			$data['meta_input']['hometown'] = qrk_sanitize_attribute( $item['hometown'] );
		}

		// Set years_with_quark.
		if ( ! empty( $item['years_with_quark'] ) && is_string( $item['years_with_quark'] ) ) {
			$data['meta_input']['years_with_quark'] = $item['years_with_quark'];
		}

		// Set expeditions_completed.
		if ( ! empty( $item['expeditions_completed'] ) && is_string( $item['expeditions_completed'] ) ) {
			$data['meta_input']['expeditions_completed'] = qrk_sanitize_attribute( $item['expeditions_completed'] );
		}

		// Set countries_travelled.
		if ( ! empty( $item['countries_travelled'] ) && is_string( $item['countries_travelled'] ) ) {
			$data['meta_input']['countries_travelled'] = qrk_sanitize_attribute( $item['countries_travelled'] );
		}

		// Set favorite_destination.
		if ( ! empty( $item['favorite_destination'] ) && is_string( $item['favorite_destination'] ) ) {
			$region_post = get_post_by_id( absint( $item['favorite_destination'] ), REGION_POST_TYPE );

			// Check if region post exists.
			if ( $region_post instanceof WP_Post ) {
				$data['meta_input']['favorite_destination'] = $region_post->ID;
			}
		}

		// Set favorite_ship.
		if ( ! empty( $item['favorite_ship'] ) && is_string( $item['favorite_ship'] ) ) {
			$ship_post = get_post_by_id( absint( $item['favorite_ship'] ), SHIP_POST_TYPE );

			// Check if ship post exists.
			if ( $ship_post instanceof WP_Post ) {
				$data['meta_input']['favorite_ship'] = $ship_post->ID;
			}
		}

		// Set email_address.
		if ( ! empty( $item['email_address'] ) && is_string( $item['email_address'] ) ) {
			$data['meta_input']['email_address'] = qrk_sanitize_attribute( $item['email_address'] );
		}

		// Set phone_extension.
		if ( ! empty( $item['phone_extension'] ) && is_string( $item['phone_extension'] ) ) {
			$data['meta_input']['phone_extension'] = qrk_sanitize_attribute( $item['phone_extension'] );
		}

		// Set phone_number.
		if ( ! empty( $item['phone_number'] ) && is_string( $item['phone_number'] ) ) {
			$data['meta_input']['phone_number'] = qrk_sanitize_attribute( $item['phone_number'] );
		}

		// Set job_title.
		if ( ! empty( $item['job_title'] ) && is_string( $item['job_title'] ) ) {
			$data['meta_input']['job_title'] = qrk_sanitize_attribute( $item['job_title'] );
		}

		// Set staff_photo_id as featured_image.
		if ( ! empty( $item['staff_photo_id'] ) ) {
			$staff_photo_id = download_file_by_mid( absint( $item['staff_photo_id'] ) );

			// Check if staff photo id exists.
			if ( ! empty( $staff_photo_id ) ) {
				$data['meta_input']['_thumbnail_id'] = $staff_photo_id;
			}
		}

		// Set spoken_languages.
		if ( ! empty( $item['spoken_languages'] ) ) {
			$spoken_language_ids  = array_map( 'absint', explode( ',', strval( $item['spoken_languages'] ) ) );
			$spoken_language_tids = [];

			// Loop through spoken_language_ids.
			foreach ( $spoken_language_ids as $spoken_language_id ) {
				$term = get_term_by_id( $spoken_language_id, SPOKEN_LANGUAGE_TAXONOMY );

				// Check if term exists.
				if ( $term instanceof WP_Term ) {
					$spoken_language_tids[] = $term->term_id;
				}
			}

			// Set spoken_languages.
			if ( ! empty( $spoken_language_tids ) ) {
				$data['tax_input'][ SPOKEN_LANGUAGE_TAXONOMY ] = $spoken_language_tids;
			}
		}

		// Set department_target_ids.
		if ( ! empty( $item['department_target_ids'] ) ) {
			$department_target_ids = array_map( 'absint', explode( ',', strval( $item['department_target_ids'] ) ) );
			$department_tids       = [];

			// Loop through department_target_ids.
			foreach ( $department_target_ids as $department_target_id ) {
				$term = get_term_by_id( $department_target_id, DEPARTMENT_TAXONOMY );

				// Check if term exists.
				if ( $term instanceof WP_Term ) {
					$department_tids[] = $term->term_id;
				}
			}

			// Set department_target_ids.
			if ( ! empty( $department_tids ) ) {
				$data['tax_input'][ DEPARTMENT_TAXONOMY ] = $department_tids;
			}
		}

		// Set staff roles.
		if ( ! empty( $item['departure_staff_roles'] ) ) {
			$staff_roles = array_map( 'absint', explode( ',', strval( $item['departure_staff_roles'] ) ) );
			$staff_tids  = [];

			// Loop through staff_roles.
			foreach ( $staff_roles as $staff_role ) {
				$term = get_term_by_id( $staff_role, DEPARTURE_STAFF_ROLE_TAXONOMY );

				// Check if term exists.
				if ( $term instanceof WP_Term ) {
					$staff_tids[] = $term->term_id;
				}
			}

			// Set staff_roles.
			if ( ! empty( $staff_tids ) ) {
				$data['tax_input'][ DEPARTURE_STAFF_ROLE_TAXONOMY ] = $staff_tids;
			}
		}

		// Set season_ids.
		if ( ! empty( $item['season_ids'] ) ) {
			$season_ids = array_map( 'trim', explode( ',', strval( $item['season_ids'] ) ) );

			// Set season_ids.
			$data['tax_input'][ SEASON_TAXONOMY ] = $season_ids;
		}

		// Set meta - drupal_id.
		$data['meta_input']['drupal_id'] = $nid;

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
			( SELECT alias AS drupal_url FROM path_alias WHERE path = CONCAT( '/node/', node.nid ) ORDER BY id DESC LIMIT 0, 1 ) AS drupal_url,
			body.body_value AS post_content,
			body.body_summary AS post_excerpt,
			field_first_name.field_first_name_value AS first_name,
			field_last_name.field_last_name_value AS last_name,
			field_hometown.field_hometown_value AS hometown,
			field_staff_member_years.field_staff_member_years_value AS years_with_quark,
			field_staff_member_expeditions.field_staff_member_expeditions_value AS expeditions_completed,
			field_staff_member_countries.field_staff_member_countries_value AS countries_travelled,
			field_staff_member_destination.field_staff_member_destination_target_id AS favorite_destination,
			field_staff_member_ship.field_staff_member_ship_target_id AS favorite_ship,
			field_email_address.field_email_address_value AS email_address,
			field_phone_number_extension.field_phone_number_extension_value AS phone_extension,
			field_phone_number.field_phone_number_value AS phone_number,
			field_job_title.field_job_title_value AS job_title,
			field_staff_photo.field_staff_photo_target_id AS staff_photo_id,
			field_metatags.field_metatags_value AS field_metatags_value,
			field_staff_member_gallery.field_staff_member_gallery_target_id AS field_staff_member_gallery_target_id,
			( SELECT GROUP_CONCAT( field_season_ids_value ORDER BY delta SEPARATOR ', ' ) FROM node__field_season_ids AS field_season_ids WHERE node.nid = field_season_ids.entity_id AND field_season_ids.langcode = node.langcode ORDER BY field_season_ids.delta ) AS season_ids,
			( SELECT GROUP_CONCAT( field_languages_target_id ORDER BY delta SEPARATOR ', ' ) FROM node__field_languages AS field_languages WHERE node.nid = field_languages.entity_id AND field_languages.langcode = node.langcode ORDER BY field_languages.delta ) AS spoken_languages,
			( SELECT GROUP_CONCAT( field_department_target_id ORDER BY delta SEPARATOR ', ' ) FROM node__field_department AS field_department WHERE node.nid = field_department.entity_id AND field_department.langcode = node.langcode ORDER BY field_department.delta ) AS department_target_ids,
			( SELECT GROUP_CONCAT( field_departure_staff_roles_target_id ORDER BY delta SEPARATOR ', ' ) FROM node__field_departure_staff_roles AS field_departure_staff_roles WHERE node.nid = field_departure_staff_roles.entity_id AND field_departure_staff_roles.langcode = node.langcode ) AS departure_staff_roles
		FROM
			node
				LEFT JOIN node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
				LEFT JOIN node__body AS body ON node.nid = body.entity_id AND node.langcode = body.langcode
				LEFT JOIN node__field_email_address AS field_email_address ON node.nid = field_email_address.entity_id AND node.langcode = field_email_address.langcode
				LEFT JOIN node__field_first_name AS field_first_name ON node.nid = field_first_name.entity_id AND node.langcode = field_first_name.langcode
				LEFT JOIN node__field_hometown AS field_hometown ON node.nid = field_hometown.entity_id AND node.langcode = field_hometown.langcode
				LEFT JOIN node__field_job_title AS field_job_title ON node.nid = field_job_title.entity_id AND node.langcode = field_job_title.langcode
				LEFT JOIN node__field_last_name AS field_last_name ON node.nid = field_last_name.entity_id AND node.langcode = field_last_name.langcode
				LEFT JOIN node__field_metatags AS field_metatags ON node.nid = field_metatags.entity_id AND node.langcode = field_metatags.langcode
				LEFT JOIN node__field_phone_number AS field_phone_number ON node.nid = field_phone_number.entity_id AND node.langcode = field_phone_number.langcode
				LEFT JOIN node__field_phone_number_extension AS field_phone_number_extension ON node.nid = field_phone_number_extension.entity_id AND node.langcode = field_phone_number_extension.langcode
				LEFT JOIN node__field_staff_member_countries AS field_staff_member_countries ON node.nid = field_staff_member_countries.entity_id AND node.langcode = field_staff_member_countries.langcode
				LEFT JOIN node__field_staff_member_destination AS field_staff_member_destination ON node.nid = field_staff_member_destination.entity_id AND node.langcode = field_staff_member_destination.langcode
				LEFT JOIN node__field_staff_member_expeditions AS field_staff_member_expeditions ON node.nid = field_staff_member_expeditions.entity_id AND node.langcode = field_staff_member_expeditions.langcode
				LEFT JOIN node__field_staff_member_gallery AS field_staff_member_gallery ON node.nid = field_staff_member_gallery.entity_id AND node.langcode = field_staff_member_gallery.langcode
				LEFT JOIN node__field_staff_member_ship AS field_staff_member_ship ON node.nid = field_staff_member_ship.entity_id AND node.langcode = field_staff_member_ship.langcode
				LEFT JOIN node__field_staff_member_years AS field_staff_member_years ON node.nid = field_staff_member_years.entity_id AND node.langcode = field_staff_member_years.langcode
				LEFT JOIN node__field_staff_photo AS field_staff_photo ON node.nid = field_staff_photo.entity_id AND node.langcode = field_staff_photo.langcode
		WHERE
			node.type = 'staff_member';";

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
