<?php
/**
 * Migrate: Taxonomies.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use WP_CLI;
use WP_CLI\ExitException;
use cli\progress\Bar;

use function Quark\Migration\Drupal\download_file_by_fid;
use function Quark\Migration\Drupal\download_file_by_mid;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\get_term_by_id;
use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

const POST_TAG_TAXONOMY      = 'post_tag';
const POST_CATEGORY_TAXONOMY = 'category';

// TODO:: Use these slugs from specific CPT/taxonomy mu-plugins.
const ACCOMMODATION_TYPES   = 'qrk_accommodation_types';
const AUDIENCES             = 'qrk_audiences';
const BRANDING              = 'qrk_branding';
const CABIN_CLASSES         = 'qrk_cabin_classes';
const CHARTER_COMPANIES     = 'qrk_charter_companies';
const DEPARTMENTS           = 'qrk_departments';
const DEPARTURE_STAFF_ROLES = 'qrk_departure_staff_roles';
const EXPEDITION_CATEGORIES = 'qrk_expedition_categories';
const INCLUSION_EXCLUSION   = 'qrk_inclusion_exclusion_category';
const SHIP_CATEGORIES       = 'qrk_ship_categories';
const ADVENTURE_OPTIONS     = 'qrk_adventure_option_category';
const ICONS                 = 'qrk_icons';
const DEPARTURE_LOCATIONS   = 'qrk_departure_locations';

/**
 * Class Media.
 */
class Taxonomies {

	/**
	 * Make sure function should not get called more than once.
	 *
	 * @var bool
	 */
	private bool $prepared_for_migration = false;

	/**
	 * Taxonomies to migrate.
	 *
	 * @var array<string, string>
	 *     Key:   WordPress Taxonomy slug.
	 *     Value: Drupal Vocabulary slug.
	 */
	public array $taxonomies = [
		POST_TAG_TAXONOMY      => 'tags',
		POST_CATEGORY_TAXONOMY => 'blog_categories',
		ACCOMMODATION_TYPES    => 'accommodation_types',
		AUDIENCES              => 'audiences',
		BRANDING               => 'branding',
		CABIN_CLASSES          => 'cabin_classes',
		CHARTER_COMPANIES      => 'charter_companies',
		DEPARTMENTS            => 'departments',
		DEPARTURE_STAFF_ROLES  => 'departure_staff_roles',
		EXPEDITION_CATEGORIES  => 'expedition_categories',
		INCLUSION_EXCLUSION    => 'inclusion_exclusion_categories',
		SHIP_CATEGORIES        => 'ship_categories',
		ADVENTURE_OPTIONS      => 'adventure_options',
		ICONS                  => 'icons',
		DEPARTURE_LOCATIONS    => 'departure_locations',
	];

	/**
	 * Migrate All Taxonomies.
	 *
	 * @param mixed[] $args       WP CLI arguments.
	 * @param mixed[] $args_assoc WP CLI associative arguments.
	 *
	 * @subcommand all
	 * @synopsis [--taxonomies=<taxonomies>]
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function all( array $args = [], array $args_assoc = [] ): void {
		// Get options.
		$options = wp_parse_args(
			$args_assoc,
			[
				'taxonomies' => [],
			]
		);

		// Get all taxonomies.
		$valid_taxonomies = array_keys( $this->taxonomies );

		// Fetch taxonomy slug from command.
		if ( ! empty( $options['taxonomies'] ) ) {
			$taxonomies_to_migrate = explode( ',', $options['taxonomies'] );

			// Validate taxonomies.
			foreach ( $taxonomies_to_migrate as $taxonomy ) {
				if ( ! array_key_exists( $taxonomy, $this->taxonomies ) ) {
					WP_CLI::error( sprintf( 'Taxonomy "%s" not exist!', $taxonomy ) );
				}
			}

			// Assign taxonomies to migrate.
			$valid_taxonomies = $taxonomies_to_migrate;
		}

		// Migrate taxonomies.
		foreach ( $valid_taxonomies as $taxonomy ) {
			// Migrate taxonomy.
			WP_CLI::log( sprintf( 'Migrating "%s" Taxonomy', $taxonomy ) );
			$this->migrate_taxonomy( $taxonomy );
			WP_CLI::log( '' );
		}
	}

	/**
	 * Migrate taxonomy from Drupal to WordPress.
	 *
	 * @param string $taxonomy Taxonomy slug.
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 */
	public function migrate_taxonomy( string $taxonomy = '' ): void {
		// Make sure taxonomy exist.
		if ( ! taxonomy_exists( $taxonomy ) ) {
			WP_CLI::error( WP_CLI::colorize( sprintf( 'Taxonomy "%s" not exist!', $taxonomy ) ) );

			// Bail out if invalid taxonomy.
			return;
		}

		// Prepare for migration and make sure function should not get called more than once.
		if ( false === $this->prepared_for_migration ) {
			prepare_for_migration();
			$this->prepared_for_migration = true;
		}

		// Fetch taxonomy data from drupal database.
		$data = $this->get_drupal_data( $taxonomy );

		// Return if unable to fetch data.
		if ( empty( $data ) ) {
			WP_CLI::error( WP_CLI::colorize( sprintf( 'Unable to fetch data for "%s" taxonomy!', $taxonomy ) ) );

			// Bail out if unable to fetch data.
			return;
		}

		// Print Log.
		WP_CLI::log( WP_CLI::colorize( '%BTotal Found: %n' . count( $data ) ) );

		// Initialize progress bar.
		$progress = make_progress_bar( $taxonomy, count( $data ) );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Start inserting terms.
		foreach ( $data as $item ) {
			// Insert terms.
			$this->insert_term( $item, $taxonomy );
			$progress->tick();
		}

		// Finish progress bar.
		$progress->finish();

		// Map migrated terms parent-child relationship.
		WP_CLI::log( 'Initiating parent-child relationship...' );
		$this->map_parent_child_relation( $data, $taxonomy );
		WP_CLI::success( 'Successfully mapped relationships.' );

		// Allow script to breath for a second,
		// Sleep for 1 second.
		sleep( 1 );
	}

	/**
	 * Insert Term by passing drupal data and WordPress Taxonomy slug.
	 *
	 * @param array<string, int|string> $drupal_term Drupal term data.
	 * @param string                    $taxonomy Taxonomy slug.
	 *
	 * @return mixed
	 */
	public function insert_term( array $drupal_term = [], string $taxonomy = '' ): mixed {
		// Normalize drupal term data.
		$normalized_term = $this->normalize_drupal_term( $drupal_term, $taxonomy );

		// Make sure data is normalized.
		if ( empty( $normalized_term ) ) {
			WP_CLI::warning( 'Unable to normalize drupal term data!' );

			// Bail out.
			return false;
		}

		// Check term exist or not.
		$term_exists = get_term_by_id( $normalized_term['meta']['drupal_term_id'], $normalized_term['taxonomy'] );

		// Update slug if term is not yet migrated from drupal but has same term with same slug.
		if ( empty( $term_exists ) ) {
			$term = get_term_by( 'slug', $normalized_term['slug'], $taxonomy );

			// Update slug if already exist.
			if ( $term instanceof \WP_Term ) {
				$normalized_term['slug'] = wp_unique_term_slug( $normalized_term['slug'], (object) $normalized_term );
			}
		}

		// Prepare arguments.
		$arguments = [
			'name'        => $normalized_term['name'],
			'slug'        => $normalized_term['slug'],
			'description' => $normalized_term['description'],
		];

		// Insert/update term.
		if ( ! empty( $term_exists ) ) {
			/**
			 * Slug might mismatch while delta migration.
			 * Because in drupal there are many duplicate terms with same name and parent ID.
			 * Due to that while updating the term it is trying to update different term with duplicate term slug.
			 *
			 * For example: There are two `Austria` terms with `Europe` parent.
			 * We need to insert both terms because there are two different `tid` which is used for creating relationships.
			 * We can't insert two terms with same slug, so we have used `wp_unique_term_slug` function to generate unique slug.
			 * So now we have one term `Austria` with `austria` slug and second `Austria` with `austria-2` slug.
			 * When we perform delta migration we can not identify that which term has modified slug.
			 * This will now cause duplicate term insertion error.
			 * To avoid that issue we have unsetted the slug argument.
			 */
			unset( $arguments['slug'] );
			$output = wp_update_term( $term_exists->term_id, $normalized_term['taxonomy'], $arguments );
		} else {
			$output = wp_insert_term( $normalized_term['name'], $normalized_term['taxonomy'], $arguments );
		}

		// Validate insert/update is successfully done.
		if ( is_wp_error( $output ) || empty( $output ) || empty( $output['term_id'] ) ) {
			WP_CLI::warning( sprintf( 'Unable to insert term "%s" in "%s" taxonomy!', $arguments['name'], $normalized_term['taxonomy'] ) );

			// Bail out unable to insert / update term.
			return $output;
		}

		// Migrate metadata.
		foreach ( $normalized_term['meta'] as $key => $value ) {
			update_term_meta( $output['term_id'], $key, $value );
		}

		// Return status of term operation.
		return $output;
	}

	/**
	 * Map parent and child relationship.
	 *
	 * @param array{}|array<int, array<string, int|string>> $data Drupal data.
	 * @param string                                        $taxonomy Taxonomy slug.
	 *
	 * @return void
	 */
	public function map_parent_child_relation( array $data = [], string $taxonomy = '' ): void {
		// Loop through data and assign parent child relationship.
		foreach ( $data as $item ) {
			// Normalize data.
			$normalize_term = $this->normalize_drupal_term( $item, $taxonomy );

			// Make sure data is normalized.
			if ( empty( $normalize_term ) ) {
				WP_CLI::warning( 'Unable to normalize drupal term data!' );

				// Continue to next term.
				continue;
			}

			// Fetch wp term by drupal data.
			$wp_term = get_term_by_id( $normalize_term['meta']['drupal_term_id'], $taxonomy );

			// Return if term not found.
			if ( false === $wp_term ) {
				WP_CLI::warning( 'Unable to fetch WP term by drupal term id!' );

				// Bail out.
				continue;
			}

			// If parent is empty then bail out.
			if ( empty( $normalize_term['parent'] ) ) {
				// Bail out.
				continue;
			}

			// Fetch parent term.
			$parent_term = get_term_by_id( $normalize_term['parent'], $taxonomy );

			// Continue if parent term not found.
			if ( false === $parent_term ) {
				WP_CLI::warning( 'Unable to fetch parent WP term by drupal term id: ' . $normalize_term['parent'] );

				// Bail out.
				continue;
			}

			// Assign parent to term.
			wp_update_term( $wp_term->term_id, $taxonomy, [ 'parent' => $parent_term->term_id ] );
		}
	}

	/**
	 * Normalize drupal term data.
	 *
	 * @param array{}|array<string, int|string> $item Drupal term data.
	 * @param string                            $taxonomy Taxonomy slug.
	 *
	 * @return array{}|array{
	 *     name: string,
	 *     taxonomy: string,
	 *     slug: string,
	 *     description: string,
	 *     parent: int,
	 *     meta: array{
	 *          drupal_term_id: int
	 *     }
	 * }
	 */
	public function normalize_drupal_term( array $item = [], string $taxonomy = '' ): array {
		// Define variable.
		$prepared_args = [];

		// Validate the data type.
		if ( ! is_array( $item ) ) {
			WP_CLI::warning( 'Passed null instead of drupal term data!' );

			// Bail out early.
			return $prepared_args;
		}

		// Define variables.
		$name = '';
		$slug = '';

		// Switch to prepare args.
		switch ( $taxonomy ) {

			// Prepare arguments generic taxonomies.
			case POST_TAG_TAXONOMY:
			case POST_CATEGORY_TAXONOMY:
			case ACCOMMODATION_TYPES:
			case AUDIENCES:
			case BRANDING:
			case CABIN_CLASSES:
			case CHARTER_COMPANIES:
			case DEPARTMENTS:
			case DEPARTURE_STAFF_ROLES:
			case EXPEDITION_CATEGORIES:
			case INCLUSION_EXCLUSION:
			case SHIP_CATEGORIES:
				// Prepare name.
				if ( is_string( $item['name'] ) && ! empty( trim( $item['name'] ) ) ) {
					$name = trim( $item['name'] );
				}

				// Prepare slug.
				if ( is_string( $item['name'] ) ) {
					$slug = trim( $item['name'] );
					$slug = sanitize_title( $slug );
				}

				// Prepare arguments.
				$prepared_args = [
					'name'        => $name,
					'slug'        => $slug,
					'taxonomy'    => $taxonomy,
					'parent'      => ! empty( $item['parent_id'] ) ? $item['parent_id'] : 0,
					'description' => ! empty( $item['description__value'] ) ? $item['description__value'] : '',
					'meta'        => [
						'drupal_term_id' => ! empty( $item['tid'] ) ? $item['tid'] : '',
					],
				];
				break;

			// Prepare arguments for adventure Options taxonomy.
			case ADVENTURE_OPTIONS:
				// Prepare name.
				if ( is_string( $item['name'] ) && ! empty( trim( $item['name'] ) ) ) {
					$name = trim( $item['name'] );
				}

				// Prepare slug.
				if ( is_string( $item['name'] ) ) {
					$slug = trim( $item['name'] );
					$slug = sanitize_title( $slug );
				}

				// Prepare arguments.
				$prepared_args = [
					'name'        => $name,
					'slug'        => $slug,
					'taxonomy'    => $taxonomy,
					'parent'      => ! empty( $item['parent_id'] ) ? $item['parent_id'] : 0,
					'description' => ! empty( $item['description__value'] ) ? $item['description__value'] : '',
					'meta'        => [
						'drupal_term_id' => ! empty( $item['tid'] ) ? $item['tid'] : '',
					],
				];

				// Prepare for ACF data for icon.
				if ( ! empty( $item['field_icon_target_id'] ) ) {
					$icon = download_file_by_fid( absint( $item['field_icon_target_id'] ) );

					// Assign icon to meta.
					if ( ! empty( $icon ) ) {
						$prepared_args['meta']['icon'] = $icon;
					}
				}

				// Prepare for ACF data for Gereric image.
				if ( ! empty( $item['field_image_target_id'] ) ) {
					$image = download_file_by_mid( absint( $item['field_image_target_id'] ) );

					// Assign image to meta.
					if ( ! empty( $image ) ) {
						$prepared_args['meta']['generic_image'] = $image;
					}
				}
				break;

			// Prepare arguments for icons taxonomy.
			case ICONS:
				// Prepare name.
				if ( is_string( $item['name'] ) && ! empty( trim( $item['name'] ) ) ) {
					$name = trim( $item['name'] );
				}

				// Prepare slug.
				if ( is_string( $item['field_symbol_id_value'] ) ) {
					$slug = trim( $item['field_symbol_id_value'] );
					$slug = sanitize_title( $slug );
				}

				// Prepare arguments.
				$prepared_args = [
					'name'        => $name,
					'slug'        => $slug,
					'taxonomy'    => $taxonomy,
					'parent'      => ! empty( $item['parent_id'] ) ? $item['parent_id'] : 0,
					'description' => ! empty( $item['description__value'] ) ? $item['description__value'] : '',
					'meta'        => [
						'drupal_term_id' => ! empty( $item['tid'] ) ? $item['tid'] : '',
					],
				];

				// Prepare for ACF data for SVG.
				if ( ! empty( $item['field_svg_file_target_id'] ) ) {
					$svg_icon = download_file_by_fid( absint( $item['field_svg_file_target_id'] ) );

					// Assign svg to meta.
					if ( ! empty( $svg_icon ) ) {
						$prepared_args['meta']['svg'] = $svg_icon;
					}
				}
				break;

			// Prepare arguments for departure locations taxonomy.
			case DEPARTURE_LOCATIONS:
				// Prepare name.
				if ( is_string( $item['name'] ) && ! empty( trim( $item['name'] ) ) ) {
					$name = trim( $item['name'] );
				}

				// Prepare slug.
				if ( is_string( $item['name'] ) ) {
					$slug = trim( $item['name'] );
					$slug = sanitize_title( $slug );
				}

				// Prepare arguments.
				$prepared_args = [
					'name'        => $name,
					'slug'        => $slug,
					'taxonomy'    => $taxonomy,
					'parent'      => ! empty( $item['parent_id'] ) ? $item['parent_id'] : 0,
					'description' => ! empty( $item['description__value'] ) ? $item['description__value'] : '',
					'meta'        => [
						'drupal_term_id' => ! empty( $item['tid'] ) ? $item['tid'] : '',
					],
				];

				// Prepare for ACF data for country field.
				if ( ! empty( $item['field_departure_country_value'] ) && is_string( $item['field_departure_country_value'] ) ) {
					$country = trim( $item['field_departure_country_value'] );

					// Assign country to meta.
					if ( ! empty( $country ) ) {
						$prepared_args['meta']['departure_country'] = $country;
					}
				}
				break;
		}

		// Sanitize the name.
		if ( ! empty( $prepared_args['name'] ) ) {
			$prepared_args['name'] = qrk_sanitize_attribute( $prepared_args['name'] );
		}

		// Return prepared args.
		return $prepared_args;
	}

	/**
	 * Fetch data from drupal database.
	 *
	 * @param string $taxonomy Taxonomy slug.
	 *
	 * @return array{}|array{0: array<string, int|string>}
	 *
	 * @throws ExitException Exit on failure of command.
	 */
	public function get_drupal_data( string $taxonomy = '' ): array {
		// Get database connection.
		$drupal_database = get_database();

		// Get query based on taxonomy.
		$query = $this->get_drupal_query( $taxonomy );

		// Return early.
		if ( empty( $query ) ) {
			WP_CLI::error( 'Unable to prepare query!' );

			// Bail out if query not found.
			return [];
		}

		// Fetch data from drupal database.
		$result = $drupal_database->get_results( $query, ARRAY_A );

		// Return early.
		if ( ! is_array( $result ) ) {
			WP_CLI::error( 'Unable to fetch data!' );

			// Bail out if data type is not array.
			return [];
		}

		// Return data.
		return $result;
	}

	/**
	 * Prepare drupal query.
	 *
	 * @param string $taxonomy Taxonomy slug.
	 *
	 * @return string
	 */
	public function get_drupal_query( string $taxonomy = '' ): string {
		// Define query.
		$query = '';

		// Switch based on taxonomy and prepare drupal query.
		switch ( $taxonomy ) {

			// Simple taxonomies.
			case POST_TAG_TAXONOMY:
			case POST_CATEGORY_TAXONOMY:
			case ACCOMMODATION_TYPES:
			case AUDIENCES:
			case BRANDING:
			case CABIN_CLASSES:
			case CHARTER_COMPANIES:
			case DEPARTMENTS:
			case DEPARTURE_STAFF_ROLES:
			case EXPEDITION_CATEGORIES:
			case INCLUSION_EXCLUSION:
			case SHIP_CATEGORIES:
				// Get drupal term slug.
				$drupal_term_slug = $this->taxonomies[ $taxonomy ];

				// Drupal query.
				$query = "SELECT
						term.`tid`,
						term.`vid`,
						term.langcode,
						parent.`parent_target_id` AS `parent_id`,
						field_data.`name`,
						field_data.`description__value`,
						( SELECT alias AS drupal_url FROM path_alias WHERE path = CONCAT( '/taxonomy/term/', term.tid ) ORDER BY id DESC LIMIT 0, 1 ) AS drupal_url
					FROM
						taxonomy_term_data AS term
							LEFT JOIN taxonomy_term__parent AS parent ON term.`tid` = parent.`entity_id` AND term.langcode = parent.langcode
							LEFT JOIN taxonomy_term_field_data AS field_data ON term.`tid` = field_data.`tid` AND term.langcode = field_data.langcode
					WHERE
						term.`vid` = '{$drupal_term_slug}'
					ORDER BY
						parent.`parent_target_id` ASC;";
				break;

			// Adventure options taxonomy drupal query.
			case ADVENTURE_OPTIONS:
				$query = "
					SELECT
						term.`tid`,
						term.`vid`,
						term.langcode,
						parent.`parent_target_id` AS `parent_id`,
						field_data.`name`,
						field_data.`description__value`,
						( SELECT alias AS drupal_url FROM path_alias WHERE path = CONCAT( '/taxonomy/term/', term.tid ) ORDER BY id DESC LIMIT 0, 1 ) AS drupal_url,
						field_icon.field_icon_target_id AS `field_icon_target_id`,
						field_image.field_image_target_id AS `field_image_target_id`
					FROM
						taxonomy_term_data AS term
						LEFT JOIN taxonomy_term__parent AS parent ON term.`tid` = parent.`entity_id` AND term.langcode = parent.langcode
						LEFT JOIN taxonomy_term_field_data AS field_data ON term.`tid` = field_data.`tid` AND term.langcode = field_data.langcode
						LEFT JOIN `taxonomy_term__field_icon` AS `field_icon` ON term.tid = field_icon.entity_id AND term.langcode = field_icon.langcode
						LEFT JOIN `taxonomy_term__field_image` AS `field_image` ON term.tid = field_image.entity_id AND term.langcode = field_image.langcode
					WHERE
						term.`vid` = 'adventure_options'
					ORDER BY
						parent.`parent_target_id` ASC;
				";
				break;

			// Icons taxonomy drupal query.
			case ICONS:
				$query = "SELECT
					term.`tid`,
					term.`vid`,
					term.langcode,
					parent.`parent_target_id` AS `parent_id`,
					field_data.`name`,
					field_data.`description__value`,
					( SELECT alias AS drupal_url FROM path_alias WHERE path = CONCAT( '/taxonomy/term/', term.tid ) ORDER BY id DESC LIMIT 0, 1 ) AS drupal_url,
					field_svg_file.field_svg_file_target_id AS `field_svg_file_target_id`,
					field_symbol_id.field_symbol_id_value AS `field_symbol_id_value`
				FROM
					taxonomy_term_data AS term
					LEFT JOIN taxonomy_term__parent AS parent ON term.`tid` = parent.`entity_id` AND term.langcode = parent.langcode
					LEFT JOIN taxonomy_term_field_data AS field_data ON term.`tid` = field_data.`tid` AND term.langcode = field_data.langcode
					LEFT JOIN `taxonomy_term__field_svg_file` AS `field_svg_file` ON term.tid = field_svg_file.entity_id AND term.langcode = field_svg_file.langcode
					LEFT JOIN `taxonomy_term__field_symbol_id` AS `field_symbol_id` ON term.tid = field_symbol_id.entity_id AND term.langcode = field_symbol_id.langcode
				WHERE
					term.`vid` = 'icons'
				ORDER BY
					parent.`parent_target_id` ASC;";
				break;

			// Departure locations taxonomy drupal query.
			case DEPARTURE_LOCATIONS:
				$query = "SELECT
					term.`tid`,
					term.`vid`,
					term.langcode,
					parent.`parent_target_id` AS `parent_id`,
					field_data.`name`,
					field_data.`description__value`,
					( SELECT alias AS drupal_url FROM path_alias WHERE path = CONCAT( '/taxonomy/term/', term.tid ) ORDER BY id DESC LIMIT 0, 1 ) AS drupal_url,
					field_departure_country.field_departure_country_value AS `field_departure_country_value`
				FROM
					taxonomy_term_data AS term
					LEFT JOIN taxonomy_term__parent AS parent ON term.`tid` = parent.`entity_id` AND term.langcode = parent.langcode
					LEFT JOIN taxonomy_term_field_data AS field_data ON term.`tid` = field_data.`tid` AND term.langcode = field_data.langcode
					LEFT JOIN `taxonomy_term__field_departure_country` AS `field_departure_country` ON term.tid = field_departure_country.entity_id AND term.langcode = field_departure_country.langcode
				WHERE
					term.`vid` = 'departure_locations'
				ORDER BY
					parent.`parent_target_id` ASC;";
				break;
		}

		// Return Drupal query.
		return $query;
	}
}
