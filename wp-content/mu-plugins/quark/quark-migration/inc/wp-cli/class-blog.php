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
use function Quark\Migration\WordPress\qrk_sanitize_attribute;
use function WP_CLI\Utils\make_progress_bar;

use const Quark\Blog\POST_TYPE;
use const Quark\Blog\Authors\POST_TYPE as AUTHOR_POST_TYPE;

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
			field_primary_blog_category.field_primary_blog_category_target_id AS primary_category_id,
			field_blog_authors.field_blog_authors_target_id AS blog_author_id
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
			node__field_blog_authors AS field_blog_authors ON node.nid = field_blog_authors.entity_id AND node.langcode = field_blog_authors.langcode
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
				'post_title'        => strval( qrk_sanitize_attribute( $blog_post['post_title'] ) ),
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

			// Set blog authors.
			if ( ! empty( $blog_post['blog_author_id'] ) ) {
				$author = get_post_by_id( $blog_post['blog_author_id'], AUTHOR_POST_TYPE );

				// Check if we have a valid author.
				if ( ! empty( $author ) ) {
					// Set author.
					update_field( 'blog_authors', [ $author->ID ], $post_id );
				}
			}

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

	/**
	 * Migrate blog authors.
	 *
	 * @subcommand authors
	 *
	 * @throws ExitException Exception on error.
	 *
	 * @return void
	 */
	public function blog_authors() {
		// Welcome message.
		WP_CLI::log( WP_CLI::colorize( '%YMigrating blog authors from Drupal...%n' ) );

		// Build query.
		$drupal_db = get_database();
		$query     = "
		SELECT
			author.id as id,
			author.title as post_title,
			author.status as post_status,
			author.description__value as post_content,
			author.created as post_date,
			author.changed as post_modified,
			photo.field_author_photo_target_id as thumbnail_id,
			title.field_author_title_value as author_title,
			first_name.field_first_name_value as first_name,
			last_name.field_last_name_value as last_name,
			linkedin.field_linkedin_url_uri as linkedin_url,
			twitter.field_twitter_username_value as twitter_username,
			website.field_website_url_uri as website_url
		FROM
			blog_author_field_data as author
				LEFT JOIN blog_author__field_author_photo as photo ON author.id = photo.entity_id
				LEFT JOIN blog_author__field_author_title as title ON author.id = title.entity_id
				LEFT JOIN blog_author__field_first_name as first_name ON author.id = first_name.entity_id
				LEFT JOIN blog_author__field_last_name as last_name ON author.id = last_name.entity_id
				LEFT JOIN blog_author__field_linkedin_url as linkedin ON author.id = linkedin.entity_id
				LEFT JOIN blog_author__field_twitter_username as twitter ON author.id = twitter.entity_id
				LEFT JOIN blog_author__field_website_url as website ON author.id = website.entity_id
		WHERE
			author.langcode = 'en'
    	";

		// Get blog authors.
		$blog_authors = $drupal_db->get_results( $query, ARRAY_A );

		// Check if we have blog authors.
		if ( empty( $blog_authors ) || ! is_array( $blog_authors ) ) {
			WP_CLI::error( 'No blog authors found!' );

			// Bail out.
			return;
		}

		// Progress bar.
		$total_blog_authors = count( $blog_authors );
		$progress           = make_progress_bar( 'Migrating', $total_blog_authors );
		$count              = 0;
		WP_CLI::log( WP_CLI::colorize( '%GFound blog authors: %n' . $total_blog_authors ) );

		// Bail out if progress bar is not found.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out.
			return;
		}

		// Prepare for migration.
		prepare_for_migration();

		// Migrate blog authors.
		foreach ( $blog_authors as $blog_author ) {
			// Insert author.
			$normalized_post = $this->normalize_drupal_post_author( $blog_author );

			// Make sure data is normalized.
			if ( empty( $normalized_post ) ) {
				WP_CLI::warning( 'Unable to normalize drupal post data!' );

				// Bail out.
				return;
			}

			// Check post exist or not.
			$wp_post = get_post_by_id( $normalized_post['meta_input']['drupal_id'], AUTHOR_POST_TYPE );

			// Insert/update post.
			if ( ! empty( $wp_post ) ) {
				$normalized_post['ID'] = $wp_post->ID;
				$output                = wp_update_post( $normalized_post );
			} else {
				// Insert post.
				$output = wp_insert_post( $normalized_post );
			}

			// Check if post inserted/updated or not.
			if ( $output instanceof WP_Error ) {
				// Print error.
				WP_CLI::warning( sprintf( 'Unable to insert/update blog author: %d', $blog_author['id'] ) );
			} else {
				++$count;
			}

			// Update progress.
			$progress->tick();
		}
	}

	/**
	 * Normalize post data for Blog Author.
	 *
	 * @param array{}|array<string, int|string> $blog_author Drupal post data.
	 *
	 * @return array{}|array{
	 *     post_type: string,
	 *     post_author: int,
	 *     post_title : string,
	 *     post_date : string,
	 *     post_date_gmt : string,
	 *     post_modified : string,
	 *     post_modified_gmt : string,
	 *     post_status : string,
	 *     comment_status: string,
	 *     ping_status: string,
	 *     meta_input : array{
	 *          drupal_id : int,
	 *     }
	 * }
	 */
	public function normalize_drupal_post_author( array $blog_author = [] ): array {
		// Bail out if empty.
		if ( empty( $blog_author ) ) {
			return [];
		}

		// Normalize data.
		$id          = ! empty( $blog_author['mid'] ) ? absint( $blog_author['id'] ) : 0;
		$title       = '';
		$created_at  = gmdate( 'Y-m-d H:i:s' );
		$modified_at = gmdate( 'Y-m-d H:i:s' );
		$status      = 'draft';

		// Title.
		if ( is_string( $blog_author['post_title'] ) && ! empty( $blog_author['post_title'] ) ) {
			$title = trim( $blog_author['post_title'] );
		}

		// Created date.
		if ( ! empty( $blog_author['created'] ) ) {
			$created_at = gmdate( 'Y-m-d H:i:s', absint( $blog_author['created'] ) );
		}

		// Modified date.
		if ( ! empty( $blog_author['changed'] ) ) {
			$modified_at = gmdate( 'Y-m-d H:i:s', absint( $blog_author['changed'] ) );
		}

		// Status.
		if ( ! empty( $blog_author['status'] ) && 1 === absint( $blog_author['status'] ) ) {
			$status = 'publish';
		}

		// Build author data.
		$data = [
			'post_type'         => AUTHOR_POST_TYPE,
			'post_author'       => 1,
			'post_title'        => $title,
			'post_date'         => $created_at,
			'post_date_gmt'     => $created_at,
			'post_modified'     => $modified_at,
			'post_modified_gmt' => $modified_at,
			'post_content'      => prepare_content( strval( $blog_author['post_content'] ) ),
			'post_status'       => $status,
			'comment_status'    => 'closed',
			'ping_status'       => 'closed',
			'meta_input'        => [
				'drupal_id' => $id,
			],
		];

		// Set Thumbnail.
		if ( ! empty( $blog_author['thumbnail_id'] ) ) {
			$wp_thumbnail_id = download_file_by_mid( absint( $blog_author['thumbnail_id'] ) );

			// Set thumbnail.
			if ( is_int( $wp_thumbnail_id ) ) {
				$data['meta_input']['_thumbnail_id'] = $wp_thumbnail_id;
			}
		}

		// Set First Name.
		if ( ! empty( $blog_author['first_name'] ) ) {
			$data['meta_input']['first_name'] = $blog_author['first_name'];
		}

		// Set Last Name.
		if ( ! empty( $blog_author['last_name'] ) ) {
			$data['meta_input']['last_name'] = $blog_author['last_name'];
		}

		// Set author title.
		if ( ! empty( $blog_author['author_title'] ) ) {
			$data['meta_input']['author_title'] = $blog_author['author_title'];
		}

		// Set website_url.
		if ( ! empty( $blog_author['website_url'] ) ) {
			$data['meta_input']['website_url'] = $blog_author['website_url'];
		}

		// Set linkedin_url.
		if ( ! empty( $blog_author['linkedin_url'] ) ) {
			$data['meta_input']['linkedin_url'] = $blog_author['linkedin_url'];
		}

		// Set twitter_username.
		if ( ! empty( $blog_author['twitter_username'] ) ) {
			$data['meta_input']['twitter_username'] = $blog_author['twitter_username'];
		}

		// Return normalized data.
		return $data;
	}
}
