<?php
/**
 * Migrate: Site Clone.
 *
 * @package quark-multilingual
 */

namespace Quark\Multilingual\WP_CLI;

use Inpsyde\MultilingualPress\Framework\Api\ContentRelations;
use Inpsyde\MultilingualPress\Framework\Database\Exception\NonexistentTable;
use Inpsyde\MultilingualPress\TranslationUi\Term\RelationshipContext as TermRelationshipContext;
use Inpsyde\MultilingualPress\TranslationUi\Post\RelationshipContext as PostRelationshipContext;
use Quark\Multilingual\MultilingualPress_Modules\Post_Meta;
use Quark\Multilingual\MultilingualPress_Modules\Term_Meta;
use WP_CLI;
use WP_CLI\ExitException;
use WP_Error;
use WP_Post;
use WP_Term;
use WP_Term_Query;

use function Quark\Multilingual\translate_block_strings;
use function Travelopia\Multilingual\get_language_from_site_id;
use function Travelopia\Multilingual\get_post_translations;
use function Travelopia\Multilingual\get_term_translations;
use function Travelopia\Translation\translate_strings;
use function Inpsyde\MultilingualPress\resolve;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\CabinCategories\CABIN_CLASS_TAXONOMY;
use const Quark\Departures\SPOKEN_LANGUAGE_TAXONOMY;
use const Quark\Expeditions\DESTINATION_TAXONOMY;
use const Quark\Expeditions\EXCURSION_TAXONOMY;
use const Quark\Expeditions\EXPEDITION_CATEGORY_TAXONOMY;
use const Quark\Expeditions\EXPEDITION_TAG_TAXONOMY;
use const Quark\InclusionSets\INCLUSION_EXCLUSION_CATEGORY;
use const Quark\Itineraries\DEPARTURE_LOCATION_TAXONOMY;
use const Quark\Itineraries\TAX_TYPE_TAXONOMY;
use const Quark\Ships\SHIP_CATEGORY_TAXONOMY;
use const Quark\StaffMembers\DEPARTMENT_TAXONOMY;
use const Quark\StaffMembers\DEPARTURE_STAFF_ROLE_TAXONOMY;
use const Quark\StaffMembers\SEASON_TAXONOMY;
use const Quark\AdventureOptions\POST_TYPE as ADVENTURE_OPTION_POST_TYPE;
use const Quark\CabinCategories\POST_TYPE as CABIN_CATEGORY_POST_TYPE;
use const Quark\PolicyPages\POST_TYPE as POLICY_PAGE_POST_TYPE;
use const Quark\ExclusionSets\POST_TYPE as EXCLUSION_SET_POST_TYPE;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Expeditions\PrePostTripOptions\POST_TYPE as PRE_POST_TRIP_POST_TYPE;
use const Quark\InclusionSets\POST_TYPE as INCLUSION_SET_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\ItineraryDays\POST_TYPE as ITINERARY_DAY_POST_TYPE;
use const Quark\LandingPages\POST_TYPE as LANDING_PAGE_POST_TYPE;
use const Quark\Offers\POST_TYPE as OFFER_POST_TYPE;
use const Quark\Ports\POST_TYPE as PORT_POST_TYPE;
use const Quark\PressReleases\POST_TYPE as PRESS_RELEASE_POST_TYPE;
use const Quark\Regions\POST_TYPE as REGION_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;
use const Quark\ShipDecks\POST_TYPE as SHIP_DECK_POST_TYPE;
use const Quark\StaffMembers\POST_TYPE as STAFF_MEMBER_POST_TYPE;

/**
 * Class Site Clone.
 */
class Site_Clone {

	/**
	 * Clone content to Target Site.
	 *
	 * @param mixed[]              $args       Arguments.
	 * @param array<string, mixed> $args_assoc Associative arguments.
	 *
	 * @subcommand process
	 *
	 * @synopsis [--dry-run] [--target-site-id=<target-site-id>]
	 *
	 * @return void
	 *
	 * @throws NonexistentTable Throws exception.
	 *
	 * @throws ExitException When error occurs.
	 */
	public function process( array $args = [], array $args_assoc = [] ): void {
		// Check if the dry-run flag is set.
		$is_dry_run = isset( $args_assoc['dry-run'] );

		// Display dry run message.
		if ( $is_dry_run ) {
			WP_CLI::log( 'Dry run enabled. No changes will be made.' . PHP_EOL );
		} else {
			WP_CLI::log( 'Dry run disabled. Changes will be made.' . PHP_EOL );
		}

		// Get options.
		$options = wp_parse_args(
			$args_assoc,
			[
				'target-site-id' => 0,
			]
		);

		// Validate options.
		if ( empty( $options['target-site-id'] ) ) {
			WP_CLI::error( 'Please provide Target Site ID.' );
		}

		// Get all sites.
		$sites    = (array) get_sites();
		$site_ids = wp_list_pluck( $sites, 'blog_id' );

		// Validate target site ID.
		if ( ! in_array( $options['target-site-id'], $site_ids, true ) ) {
			WP_CLI::error( 'Invalid Target Site ID.' );
		}

		// Get source and target site ID.
		$source_site_id = get_current_blog_id();
		$target_site_id = absint( $options['target-site-id'] );

		// Initialize variables.
		$terms_count = 0;
		$posts_count = 0;

		/**
		 * Get All Terms and it's data from Source Site.
		 */
		$taxonomy_query = new WP_Term_Query(
			[
				'hide_empty' => false,
				'orderby'    => 'parent',
				'order'      => 'ASC',
				'taxonomy'   => [
					ADVENTURE_OPTION_CATEGORY,
					CABIN_CLASS_TAXONOMY,
					DEPARTMENT_TAXONOMY,
					DEPARTURE_LOCATION_TAXONOMY,
					DESTINATION_TAXONOMY,
					EXCURSION_TAXONOMY,
					EXPEDITION_CATEGORY_TAXONOMY,
					EXPEDITION_TAG_TAXONOMY,
					INCLUSION_EXCLUSION_CATEGORY,
					SEASON_TAXONOMY,
					SHIP_CATEGORY_TAXONOMY,
					SPOKEN_LANGUAGE_TAXONOMY,
					DEPARTURE_STAFF_ROLE_TAXONOMY,
					TAX_TYPE_TAXONOMY,
				],
			]
		);

		// Get terms.
		$terms = $taxonomy_query->terms;

		// Loop through terms.
		foreach ( $terms as $term ) {
			// Validate term.
			if ( ! $term instanceof WP_Term ) {
				continue;
			}

			// Get original term and parent term id.
			$term_id = absint( $term->term_id );

			// Get translated term ID.
			$translated_term_id = $this->get_translated_term_id( $term_id, $source_site_id, $target_site_id );

			// Check if term is already migrated.
			if ( ! empty( $translated_term_id ) ) {
				continue;
			}

			// Log for dry run.
			if ( $is_dry_run ) {
				// Log the message.
				WP_CLI::log( 'Source Term ID : ' . $term_id );

				// Increment terms count.
				++$terms_count;

				// Continue.
				continue;
			}

			// Migrate terms.
			$translated_term_id = $this->translate_and_get_term( $term_id, $source_site_id, $target_site_id );

			// Increment terms count.
			++$terms_count;

			// Check if term is migrated.
			if ( $translated_term_id instanceof WP_Error ) {
				WP_CLI::warning( 'Source Term ID : ' . $term_id . ' | Error : ' . $translated_term_id->get_error_message() );
			} else {
				WP_CLI::success( 'Source Term ID : ' . $term_id . ' | Translated Term ID: ' . $translated_term_id );
			}
		}

		// Log terms migration completion.
		WP_CLI::log( PHP_EOL . PHP_EOL );
		WP_CLI::log( 'Terms migration completed.' );

		// Log posts migration start.
		WP_CLI::log( PHP_EOL . PHP_EOL );
		WP_CLI::log( 'Starting Posts migration.' );

		// Global variables.
		global $wpdb;

		// List of allowed post types.
		$post_types = [
			ADVENTURE_OPTION_POST_TYPE,
			POLICY_PAGE_POST_TYPE,
			CABIN_CATEGORY_POST_TYPE,
			EXCLUSION_SET_POST_TYPE,
			EXPEDITION_POST_TYPE,
			INCLUSION_SET_POST_TYPE,
			ITINERARY_POST_TYPE,
			ITINERARY_DAY_POST_TYPE,
			LANDING_PAGE_POST_TYPE,
			OFFER_POST_TYPE,
			PORT_POST_TYPE,
			PRE_POST_TRIP_POST_TYPE,
			PRESS_RELEASE_POST_TYPE,
			REGION_POST_TYPE,
			SHIP_POST_TYPE,
			SHIP_DECK_POST_TYPE,
			STAFF_MEMBER_POST_TYPE,
			'wp_block',
			'page',
			'wp_template',
			'wp_template_part',
		];

		// List of allowed post status.
		$post_status = [
			'publish',
			'draft',
			'future',
		];

		// Get posts.
		// phpcs:disable WordPress.DB.PreparedSQL.NotPrepared
		$posts = (array) $wpdb->get_results(
			"
			SELECT
				ID,
				post_parent,
				post_type
			FROM
				$wpdb->posts AS posts
			WHERE
				post_type IN ('" . implode( "','", $post_types ) . "') AND
				post_status IN ('" . implode( "','", $post_status ) . "')
			ORDER BY
				post_parent, ID
			",
			ARRAY_A
		);
		// phpcs:enable WordPress.DB.PreparedSQL.NotPrepared

		// Migrate posts.
		foreach ( $posts as $the_post ) {
			// Get original post and translations.
			$post_id = absint( $the_post['ID'] );

			// Get translated post ID.
			$existing_translated_post_id = $this->get_translated_post_id( $post_id, $source_site_id, $target_site_id );

			// Log for dry run.
			if ( $is_dry_run ) {
				// Log the message.
				WP_CLI::log( 'Source Post ID : ' . $post_id );

				// Increment posts count.
				++$posts_count;

				// Continue.
				continue;
			}

			// Migrate posts.
			$translated_post_id = $this->translate_and_get_post( $post_id, $source_site_id, $target_site_id );

			// Increment posts count.
			++$posts_count;

			// Check if post is migrated.
			if ( $translated_post_id instanceof WP_Error ) {
				WP_CLI::warning( 'Source Post ID : ' . $post_id . ' | Error : ' . $translated_post_id->get_error_message() );

				// Continue.
				continue;
			}

			// Log the message.
			if ( ! empty( $existing_translated_post_id ) ) {
				WP_CLI::success( 'Source Post ID : ' . $post_id . ' | Translated Post ID : ' . $translated_post_id . ' | Updated' );
			} else {
				WP_CLI::success( 'Source Post ID : ' . $post_id . ' | Translated Post ID : ' . $translated_post_id );
			}
		}

		// Flush the cache.
		wp_cache_flush();

		// Log posts migration completion.
		WP_CLI::log( PHP_EOL . PHP_EOL );
		WP_CLI::log( 'Posts migration completed.' );

		// Log terms and posts count.
		WP_CLI::log( PHP_EOL . PHP_EOL );
		WP_CLI::success( 'Migration completed.' );
		WP_CLI::success( 'Migrated Terms ' . $terms_count . ' Out of ' . count( $terms ) );
		WP_CLI::success( 'Migrated Posts ' . $posts_count . ' Out of ' . count( $posts ) );
	}

	/**
	 * Get Translated Term ID.
	 *
	 * @param int $original_term_id Original term ID.
	 * @param int $source_site_id   Source site ID.
	 * @param int $target_site_id   Target site ID.
	 *
	 * @return int Translated term ID.
	 */
	private function get_translated_term_id( int $original_term_id = 0, int $source_site_id = 0, int $target_site_id = 0 ): int {
		// Validate original term ID, source site ID and target site ID.
		if ( empty( $original_term_id ) || empty( $source_site_id ) || empty( $target_site_id ) ) {
			return 0;
		}

		// Initialize translated term ID.
		$translated_term_id = 0;

		// get translated term IDs.
		$term_translations = get_term_translations( $original_term_id, $source_site_id );

		// Loop through term translations.
		if ( ! empty( $term_translations ) ) {
			foreach ( $term_translations as $term_translation ) {
				if ( $target_site_id === $term_translation['site_id'] && ! empty( $term_translation['term_id'] ) ) {
					$translated_term_id = absint( $term_translation['term_id'] );
					break;
				}
			}
		}

		// Return translated term ID.
		return $translated_term_id;
	}

	/**
	 * Create and Translate Term.
	 *
	 * @param int $source_term_id Source term ID.
	 * @param int $source_site_id Source site ID.
	 * @param int $target_site_id Target site ID.
	 *
	 * @throws NonexistentTable Throws exception.
	 *
	 * @return int|WP_Error Translated term ID.
	 */
	private function translate_and_get_term( int $source_term_id = 0, int $source_site_id = 0, int $target_site_id = 0 ): int|WP_Error {
		// Validate source term ID, source site ID and target site ID.
		if ( empty( $source_term_id ) || empty( $source_site_id ) || empty( $target_site_id ) ) {
			return new WP_Error(
				'qrk_migration_error',
				'Invalid source term ID, source site ID or target site ID.'
			);
		}

		// Get translated term ID.
		$translated_term_id = $this->get_translated_term_id( $source_term_id, $source_site_id, $target_site_id );

		// Check if term is already migrated.
		if ( ! empty( $translated_term_id ) ) {
			return $translated_term_id;
		}

		// Get source term and term meta.
		$term = get_term( $source_term_id );

		// Check if term is not empty.
		if ( ! $term instanceof WP_Term ) {
			return new WP_Error(
				'qrk_migration_error',
				'Invalid source term ID.'
			);
		}

		// Get term data.
		$parent_id        = absint( $term->parent );
		$source_term_meta = (array) get_term_meta( $term->term_id );

		// Initialize translated parent term ID.
		$translated_term_id        = 0;
		$translated_parent_term_id = 0;

		// Translate parent term.
		if ( ! empty( $parent_id ) ) {
			$translated_parent_term_id = $this->translate_and_get_term( $parent_id, $source_site_id, $target_site_id );
		}

		// Switch to target Site.
		switch_to_blog( $target_site_id );

		// Get language.
		$from_language = get_language_from_site_id( $source_site_id );
		$to_language   = get_language_from_site_id( $target_site_id );

		// Initialize translated name and description.
		$translated_name        = $term->name;
		$translated_description = $term->description;

		// Translate content.
		$value = translate_strings(
			[
				$term->name,
				$term->description,
			],
			$to_language,
			$from_language
		);

		// Check if we have a translated value and add to translations.
		if ( is_array( $value ) && ! empty( $value ) ) {
			$translated_name        = $value[0] ?? '';
			$translated_description = $value[1] ?? '';
		}

		// Create term.
		$translated_term = wp_insert_term(
			$translated_name,
			$term->taxonomy,
			[
				'parent'      => $translated_parent_term_id,
				'slug'        => $term->slug,
				'description' => $translated_description,
			]
		);

		// Check if term is created.
		if ( $translated_term instanceof WP_Error ) {
			// Restore original site.
			restore_current_blog();

			// Return error.
			return $translated_term;
		}

		// Get translated term ID.
		$translated_term_id = absint( $translated_term['term_id'] );

		// Create relationship context.
		$relationship_context = new TermRelationshipContext(
			[
				'remote_term_id' => $translated_term_id,
				'remote_site_id' => $target_site_id,
				'source_term_id' => $source_term_id,
				'source_site_id' => $source_site_id,
			]
		);

		// Translate term meta.
		$translated_term_meta = Term_Meta::filter_meta_values( $source_term_meta, $relationship_context );

		// Update terms meta.
		foreach ( $translated_term_meta as $meta_key => $meta_value ) {
			if ( is_array( $meta_value ) && ! empty( $meta_value[0] ) ) {
				// Update term meta.
				update_term_meta( $translated_term_id, strval( $meta_key ), $meta_value[0] );
			}
		}

		// Get translated term IDs.
		$term_translations_data = get_term_translations( $source_term_id, $source_site_id );
		$term_translations      = [];

		// Loop through term translations.
		foreach ( $term_translations_data as $term_translation_data ) {
			$term_translations[ $term_translation_data['site_id'] ] = $term_translation_data['term_id'];
		}

		// Add target site term ID.
		$term_translations[ $source_site_id ] = $source_term_id;
		$term_translations[ $target_site_id ] = $translated_term_id;

		// Create relationship.
		$this->create_relationship( $term_translations, 'term' );

		// Restore original site.
		restore_current_blog();

		// Return translated term ID.
		return $translated_term_id;
	}

	/**
	 * Get Translated Post ID.
	 *
	 * @param int $original_post_id Original post ID.
	 * @param int $source_site_id   Source site ID.
	 * @param int $target_site_id   Target site ID.
	 *
	 * @return int Translated post ID.
	 */
	private function get_translated_post_id( int $original_post_id = 0, int $source_site_id = 0, int $target_site_id = 0 ): int {
		// Validate original post ID and target site ID.
		if ( empty( $original_post_id ) || empty( $target_site_id ) ) {
			return 0;
		}

		// Initialize translated post ID.
		$translated_post_id = 0;

		// Get post translations.
		$post_translations = get_post_translations( $original_post_id, $source_site_id );

		// Loop through post translations.
		if ( ! empty( $post_translations ) ) {
			foreach ( $post_translations as $post_translation ) {
				if ( $target_site_id === $post_translation['site_id'] && ! empty( $post_translation['post_id'] ) ) {
					$translated_post_id = absint( $post_translation['post_id'] );
					break;
				}
			}
		}

		// Return translated post ID.
		return $translated_post_id;
	}

	/**
	 * Create and Translate Post.
	 *
	 * @param int $source_post_id Source post ID.
	 * @param int $source_site_id Source site ID.
	 * @param int $target_site_id Target site ID.
	 *
	 * @throws NonexistentTable Throws exception.
	 *
	 * @return int|WP_Error Translated post ID.
	 */
	private function translate_and_get_post( int $source_post_id = 0, int $source_site_id = 0, int $target_site_id = 0 ): int|WP_Error {
		// Validate source post ID, source site ID and target site ID.
		if ( empty( $source_post_id ) || empty( $source_site_id ) || empty( $target_site_id ) ) {
			return new WP_Error(
				'qrk_migration_error',
				'Invalid source post ID, source site ID or target site ID.'
			);
		}

		// Get translated post ID.
		$translated_post_id = $this->get_translated_post_id( $source_post_id, $source_site_id, $target_site_id );

		// Get source post and post meta.
		$post = $this->get_post( $source_post_id );

		// Validate post data.
		if ( ! $post['post'] instanceof WP_Post ) {
			return new WP_Error(
				'qrk_migration_error',
				'Invalid source post ID.'
			);
		}

		// Initialize parent post ID.
		$translated_parent_post_id = 0;

		// Translate parent post.
		if ( ! empty( $post['post']->post_parent ) ) {
			// Get parent post ID.
			$parent_post_id = $post['post']->post_parent;

			// Get translated parent post ID.
			$translated_parent_post_id = $this->get_translated_post_id( $parent_post_id, $source_site_id, $target_site_id );

			// Check if parent post is already migrated.
			if ( empty( $translated_parent_post_id ) ) {
				// Translate parent post.
				$translated_parent_post_id = $this->translate_and_get_post( $parent_post_id, $source_site_id, $target_site_id );
			}
		}

		// Create relationship context.
		$relationship_context = new PostRelationshipContext(
			[
				'remote_post_id' => 0,
				'remote_site_id' => $target_site_id,
				'source_post_id' => $source_post_id,
				'source_site_id' => $source_site_id,
			]
		);

		// Translate post meta.
		$translated_post_meta = Post_Meta::filter_post_meta_values( $post['post_meta'], $relationship_context );

		// Switch to target Site.
		switch_to_blog( $target_site_id );

		// Prepare post data.
		$post_data = [
			'post_title'        => $post['post']->post_title,
			'post_name'         => $post['post']->post_name,
			'post_author'       => $post['post']->post_author,
			'post_content'      => $post['post']->post_content,
			'post_excerpt'      => $post['post']->post_excerpt,
			'post_status'       => $post['post']->post_status,
			'post_type'         => $post['post']->post_type,
			'post_date'         => $post['post']->post_date,
			'post_date_gmt'     => $post['post']->post_date_gmt,
			'post_modified'     => $post['post']->post_modified,
			'post_modified_gmt' => $post['post']->post_modified_gmt,
			'post_parent'       => $translated_parent_post_id,
		];

		// Check if post meta is not empty, and fill the data.
		if ( ! empty( $translated_post_meta ) && is_array( $translated_post_meta ) ) {
			$post_data['meta_input'] = array_map(
				fn( $value ) => maybe_unserialize( is_array( $value ) && ! empty( $value ) ? $value[0] : '' ),
				$translated_post_meta
			);
		}

		// Filter post data.
		$post_data = (array) apply_filters(
			'multilingualpress.new_relate_remote_post_before_insert', // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
			$post_data,
			$relationship_context,
			'new'
		);

		// Get list of post types where we can use inherit content block.
		$post_types_that_use_inherit_content = get_post_types(
			[
				'public'       => true,
				'show_in_rest' => true,
			],
		);

		// If post type does not support Gutenberg then translate the content.
		if ( ! in_array( $post_data['post_type'], $post_types_that_use_inherit_content, true ) ) {
			// Get language.
			$from_language = get_language_from_site_id( $source_site_id );
			$to_language   = get_language_from_site_id( $target_site_id );

			// Translate content.
			$content_list              = translate_block_strings( [ strval( $post_data['post_content'] ) ], $to_language, $from_language );
			$post_data['post_content'] = $content_list[0] ?? '';
		}

		// Remove the slashes.
		$post_data['post_content'] = strval( wp_slash( $post_data['post_content'] ) );

		// Check if translated post is existing or not.
		if ( empty( $translated_post_id ) ) {
			// If not then Create post.
			$translated_post_id = wp_insert_post( $post_data );
		} else {
			// If existing then Update post.
			// Update post ID.
			$post_data['ID'] = $translated_post_id;

			// Update post.
			$translated_post_id = wp_update_post( $post_data );
		}

		// Check if post is created.
		if ( $translated_post_id instanceof WP_Error ) {
			// Restore original site.
			restore_current_blog();

			// Return error.
			return $translated_post_id;
		}

		// Migrate post taxonomies.
		if ( ! empty( $post['post_taxonomies'] ) ) {
			foreach ( $post['post_taxonomies'] as $taxonomy => $taxonomy_terms ) {
				// Check if taxonomy terms are not empty, and fill the data.
				if ( ! is_array( $taxonomy_terms ) ) {
					continue;
				}

				// Loop through taxonomy terms.
				foreach ( $taxonomy_terms as $taxonomy_term ) {
					// Check if term ID is empty.
					if ( ! is_array( $taxonomy_term ) || empty( $taxonomy_term['term_id'] ) ) {
						continue;
					}

					// Get original term ID.
					$term_id = absint( $taxonomy_term['term_id'] );

					// Get translated term ID.
					$translated_term_id = $this->get_translated_term_id( $term_id, $source_site_id, $target_site_id );

					// Check if we have translated term or not.
					if ( ! empty( $translated_term_id ) ) {
						// If we have translated term then use it.
						wp_set_object_terms( $translated_post_id, $translated_term_id, $taxonomy, true );
					} else {
						// If we don't have translated term then use the slug.
						wp_set_object_terms( $translated_post_id, [ $taxonomy_term['slug'] ], $taxonomy, true );
					}
				}
			}
		}

		// Get relationship ID.
		$relationship_data = get_post_translations( $source_post_id, $source_site_id );
		$relationship_ids  = [];

		// Check if relationship data is not empty, and fill the data.
		if ( ! empty( $relationship_data ) ) {
			foreach ( $relationship_data as $relationship ) {
				$relationship_ids[ $relationship['site_id'] ] = $relationship['post_id'];
			}
		}

		// Add source and target site post ID.
		$relationship_ids[ $source_site_id ] = $source_post_id;
		$relationship_ids[ $target_site_id ] = $translated_post_id;

		// Create relationship.
		$this->create_relationship( $relationship_ids, 'post' );

		// Restore original site.
		restore_current_blog();

		// Return translated post ID.
		return $translated_post_id;
	}

	/**
	 * Get Post and it's data.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return array{
	 *     post: WP_Post|null,
	 *     post_meta: mixed[],
	 *     post_taxonomies: mixed[],
	 * } Post data.
	 */
	private function get_post( int $post_id = 0 ): array {
		// Get post.
		$post = get_post( $post_id );

		// Check if post is not empty.
		if ( ! $post instanceof WP_Post ) {
			return [
				'post'            => null,
				'post_meta'       => [],
				'post_taxonomies' => [],
			];
		}

		// Grab all taxonomy terms from the database.
		global $wpdb;

		// Initialize post taxonomies.
		$post_taxonomies = [];

		// Get taxonomy terms.
		$taxonomy_terms = $wpdb->get_results(
			$wpdb->prepare(
				"
				SELECT
					t.*,
					tt.taxonomy,
					tt.description,
					tt.parent
				FROM
					$wpdb->term_relationships AS tr
				LEFT JOIN
					$wpdb->term_taxonomy AS tt ON tt.term_taxonomy_id = tr.term_taxonomy_id
				LEFT JOIN
					$wpdb->terms AS t ON t.term_id = tt.term_taxonomy_id
				WHERE
					tr.object_id = %d
				ORDER BY
					t.name
				",
				[
					$post_id,
				]
			),
			ARRAY_A
		);

		// Check if taxonomy terms are not empty, and fill the data.
		if ( ! empty( $taxonomy_terms ) ) {
			foreach ( $taxonomy_terms as $taxonomy_term ) {
				if ( ! array_key_exists( $taxonomy_term['taxonomy'], $post_taxonomies ) ) {
					$post_taxonomies[ $taxonomy_term['taxonomy'] ] = [];
				}
				$post_taxonomies[ $taxonomy_term['taxonomy'] ][] = $taxonomy_term;
			}
		}

		// Return post data.
		return [
			'post'            => $post,
			'post_meta'       => (array) get_post_meta( $post_id ),
			'post_taxonomies' => $post_taxonomies,
		];
	}

	/**
	 * Create relationship among Object IDs.
	 *
	 * @param array<int, int> $content_ids Object IDs.
	 * @param string          $type        Type.
	 *
	 * @throws NonexistentTable Throws exception.
	 *
	 * @return int
	 */
	private function create_relationship( array $content_ids = [], string $type = 'post' ): int {
		/**
		 * Content relations API instance.
		 *
		 * @var ContentRelations $api
		 */
		$api = resolve( ContentRelations::class );

		// Return relationship ID.
		return $api->createRelationship( $content_ids, $type );
	}
}
