<?php
/**
 * Migrate: Blog Posts.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use cli\progress\Bar;
use WP_CLI;
use WP_CLI\ExitException;
use WP_Error;

use function Quark\Migration\Drupal\download_file_by_mid;
use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\get_post_by_id;
use function Quark\Migration\Drupal\prepare_for_migration;
use function Quark\Migration\Drupal\prepare_content;
use function Quark\Migration\Drupal\get_term_by_id;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\Blog\POST_TYPE;

/**
 * Class Blog.
 */
class Blog {

	/**
	 * Migrate blog posts.
	 *
	 * @param mixed[] $args       WP CLI arguments.
	 * @param mixed[] $args_assoc WP CLI associative arguments.
	 *
	 * @subcommand posts
	 * @synopsis [--ids=<1,2>]
	 *
	 * @throws ExitException Exception on error.
	 *
	 * @return void
	 */
	public function posts( array $args = [], array $args_assoc = [] ): void {
		// Get options.
		$options = wp_parse_args(
			$args_assoc,
			[
				'ids' => [],
			]
		);

		// Prepare IDs.
		if ( ! empty( $options['ids'] ) ) {
			$options['ids'] = array_map( 'absint', array_filter( array_map( 'trim', explode( ',', $options['ids'] ) ) ) );
		} else {
			$options['ids'] = [];
		}

		// Welcome message.
		WP_CLI::log( WP_CLI::colorize( '%YMigrating blog posts from Drupal...%n' ) );

		// Build query.
		$drupal_db = get_database();
		$query     = "
		SELECT
			node.nid as drupal_id,
			field_data.status as post_status,
			field_data.title as post_title,
			field_data.created as post_date,
			field_data.changed as post_modified,
			body.body_value AS post_content,
			body.body_summary AS post_excerpt,
			REPLACE( path_alias.alias, '/blog/', '' ) AS post_name,
			field_blog_image.field_blog_image_target_id AS image_id,
			field_blog_read_time.field_blog_read_time_minutes AS read_time_minutes,
			field_blog_read_time.field_blog_read_time_seconds AS read_time_seconds,
			field_metatags.field_metatags_value AS seo_metatags_data,
			field_primary_blog_category.field_primary_blog_category_target_id AS primary_category_id
		FROM
			node
		LEFT JOIN
			node_field_data AS field_data ON node.nid = field_data.nid AND node.langcode = field_data.langcode
		LEFT JOIN
			node__body AS body ON node.nid = body.entity_id AND node.langcode = body.langcode
		LEFT JOIN
			node__field_blog_image AS field_blog_image ON node.nid = field_blog_image.entity_id AND node.langcode = field_blog_image.langcode
		LEFT JOIN
			node__field_blog_read_time AS field_blog_read_time ON node.nid = field_blog_read_time.entity_id AND node.langcode = field_blog_read_time.langcode
		LEFT JOIN
			node__field_blog_url AS field_blog_url ON node.nid = field_blog_url.entity_id AND node.langcode = field_blog_url.langcode
		LEFT JOIN
			node__field_metatags AS field_metatags ON node.nid = field_metatags.entity_id AND node.langcode = field_metatags.langcode
		LEFT JOIN
			node__field_primary_blog_category AS field_primary_blog_category ON node.nid = field_primary_blog_category.entity_id AND node.langcode = field_primary_blog_category.langcode
		LEFT JOIN
			path_alias ON path = CONCAT( '/node/', node.nid )
		WHERE
			node.type = 'blog'";

		// Add specified IDs to query if passed.
		if ( ! empty( $options['ids'] ) ) {
			$query .= ' AND node.nid IN (' . implode( ',', $options['ids'] ) . ')';
		}

		// Get blog posts.
		$blog_posts = $drupal_db->get_results( $query, ARRAY_A );

		// Check if we have blog posts.
		if ( empty( $blog_posts ) || ! is_array( $blog_posts ) ) {
			WP_CLI::error( 'No blog posts found!' );

			// Bail out.
			return;
		}

		// Progress bar.
		$total_blog_posts = count( $blog_posts );
		$progress         = make_progress_bar( 'Migrating', $total_blog_posts );
		$count            = 0;
		WP_CLI::log( WP_CLI::colorize( '%GFound blog posts: %n' . $total_blog_posts ) );

		// Bail out if progress bar is not found.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out.
			return;
		}

		// Prepare for migration.
		prepare_for_migration();

		// Migrate blog posts.
		foreach ( $blog_posts as $blog_post ) {
			// Insert post.
			$progress->tick();

			// check if post_name has / in it.
			if ( str_contains( $blog_post['post_name'], '/' ) ) {
				// break the post_name into parts. and get the last part of the post_name.
				$post_name_parts        = explode( '/', $blog_post['post_name'] );
				$blog_post['post_name'] = end( $post_name_parts );
			}

			// Build post data.
			$data = [
				'post_type'         => 'post',
				'post_author'       => 1,
				'post_title'        => $blog_post['post_title'],
				'post_date'         => gmdate( 'Y-m-d H:i:s', absint( $blog_post['post_date'] ) ),
				'post_date_gmt'     => gmdate( 'Y-m-d H:i:s', absint( $blog_post['post_date'] ) ),
				'post_modified'     => gmdate( 'Y-m-d H:i:s', absint( $blog_post['post_modified'] ) ),
				'post_modified_gmt' => gmdate( 'Y-m-d H:i:s', absint( $blog_post['post_modified'] ) ),
				'post_name'         => $blog_post['post_name'],
				'post_content'      => prepare_content( strval( $blog_post['post_content'] ) ),
				'post_excerpt'      => trim( wp_strip_all_tags( $blog_post['post_excerpt'] ?? '' ) ),
				'post_status'       => '1' === $blog_post['post_status'] ? 'publish' : 'draft',
				'comment_status'    => 'closed',
				'ping_status'       => 'closed',
				'meta_input'        => [
					'drupal_id' => $blog_post['drupal_id'],
				],
			];

			// Get Featured image.
			if ( ! empty( $blog_post['image_id'] ) ) {
				$wp_thumbnail_id = download_file_by_mid( $blog_post['image_id'] );

				// Set featured image.
				if ( is_int( $wp_thumbnail_id ) ) {
					$data['meta_input']['_thumbnail_id'] = $wp_thumbnail_id;
				}
			}

			// SEO meta data.
			if ( ! empty( $blog_post['seo_metatags_data'] ) ) {
				$seo_meta_data = maybe_unserialize( $blog_post['seo_metatags_data'] );

				// Check if we have SEO metadata.
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
					if ( ! empty( $seo_meta_data['title']['value'] ) ) {
						$data['meta_input']['_yoast_wpseo_title'] = str_replace(
							$search_for,
							$replace_with,
							trim( $seo_meta_data['title']['value'] )
						);
					}

					// Process seo meta description for WP SEO plugin.
					if ( ! empty( $seo_meta_data['description']['value'] ) ) {
						$data['meta_input']['_yoast_wpseo_metadesc'] = str_replace(
							$search_for,
							$replace_with,
							trim( $seo_meta_data['description']['value'] )
						);
					}
				}
			}

			// Set Read time metadata.
			if ( ! empty( $blog_post['read_time_minutes'] ) ) {
				$data['meta_input']['read_time_minutes'] = absint( $blog_post['read_time_minutes'] );
			}

			// Set read time seconds.
			if ( ! empty( $blog_post['read_time_seconds'] ) ) {
				$data['meta_input']['read_time_seconds'] = absint( $blog_post['read_time_seconds'] );
			}

			// Check post exist or not.
			$wp_post = get_post_by_id( $blog_post['drupal_id'], POST_TYPE );

			// Insert/update post.
			if ( ! empty( $wp_post ) ) {
				$data['ID'] = $wp_post->ID;
				$post_id    = wp_update_post( $data );
			} else {
				$post_id = wp_insert_post( $data );
			}

			// Update progress if post was created.
			if ( $post_id instanceof WP_Error ) {
				WP_CLI::warning( sprintf( 'Failed to import blog post - %d', $blog_post['drupal_id'] ) );
				$progress->tick();
				continue;
			}

			// Get Categories.
			$categories = (array) $drupal_db->get_results(
				strval(
					$drupal_db->prepare(
						'
						SELECT
							field_blog_categories_target_id AS drupal_term_id
						FROM
							node__field_blog_categories
						WHERE
							entity_id = %d
						ORDER BY
							delta ASC',
						$blog_post['drupal_id']
					)
				),
				ARRAY_A
			);

			// Set categories to post.
			if ( ! empty( $categories ) ) {
				$category_count = 0;

				// Loop through categories.
				foreach ( $categories as $category ) {
					$term = get_term_by_id( $category['drupal_term_id'], 'category' );

					// Check if we have a valid category.
					if ( empty( $term ) ) {
						continue;
					}

					// Set category.
					$result = wp_set_object_terms( $post_id, $term->term_id, 'category', 0 !== $category_count );

					// Update category count.
					if ( ! $result instanceof WP_Error ) {
						++$category_count;
					}
				}
			}

			// Get Tags.
			$tags = (array) $drupal_db->get_results(
				strval(
					$drupal_db->prepare(
						'
						SELECT
							field_tags_target_id AS drupal_term_id
						FROM
							node__field_tags
						WHERE
							entity_id = %d
						ORDER BY
							delta ASC
						',
						$blog_post['drupal_id']
					)
				),
				ARRAY_A
			);

			// Set tags to post.
			if ( ! empty( $tags ) ) {
				// Loop through tags.
				foreach ( $tags as $tag ) {
					$term = get_term_by_id( $tag['drupal_term_id'], 'post_tag' );

					// Check if we have a valid tag.
					if ( empty( $term ) ) {
						continue;
					}

					// Set tag.
					wp_set_object_terms( $post_id, $term->term_id, 'post_tag', true );
				}
			}

			// Set primary category.
			if ( ! empty( $blog_post['primary_category_id'] ) ) {
				$term = get_term_by_id( $blog_post['primary_category_id'], 'category' );

				// Check if we have a valid category.
				if ( ! empty( $term ) ) {
					// Set primary category.
					update_post_meta( $post_id, '_yoast_wpseo_primary_category', $term->term_id );
				}
			}

			// TODO:: Blog Authors data to map with WP user.
			// Update progress.
			++$count;
			$progress->tick();

			// Sleep for a while after every 100 posts.
			if ( 0 === $count % 100 ) {
				sleep( 2 );
			}
		}

		// All done!
		$progress->finish();
		WP_CLI::success( "Migrated $count out of $total_blog_posts." );
	}
}
