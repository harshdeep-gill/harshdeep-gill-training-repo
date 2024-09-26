<?php
/**
 * Migrate: SEO.
 *
 * @package quark-migration
 */

namespace Quark\Migration\WP_CLI;

use cli\progress\Bar;
use WP_CLI;
use WP_CLI\ExitException;
use Red_Url_Match;

use function Quark\Migration\Drupal\download_file_by_fid;
use function Quark\Migration\Drupal\get_database;
use function Quark\Migration\Drupal\get_wp_permalink;
use function Quark\Migration\Drupal\prepare_for_migration;
use function WP_CLI\Utils\make_progress_bar;

/**
 * Class SEO.
 */
class SEO {

	/**
	 * Migrate 301 redirects for new permalinks.
	 *
	 * @subcommand redirect-new-permalinks
	 *
	 * @return void
	 *
	 * @throws ExitException Exit on failure of command.
	 **/
	public function redirect_new_permalinks(): void {
		// Welcome message.
		WP_CLI::log( WP_CLI::colorize( '%YMigrating permalink redirects...%n' ) );

		// Build query.
		global $wpdb;
		$query = "
		SELECT
			p.post_type,
			p.ID,
			p.post_status,
			pm1.meta_value AS drupal_id
		FROM
			wp_posts AS p
		LEFT JOIN
			wp_postmeta AS pm1 ON pm1.post_id = p.ID AND pm1.meta_key = 'drupal_id'
		WHERE
			pm1.meta_value <> ''
		";

		// Get posts.
		$posts = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore

		// Check if we have posts.
		if ( empty( $posts ) ) {
			WP_CLI::error( 'No posts found!' );
		}

		// Progress bar.
		$total_posts = count( $posts );
		$progress    = make_progress_bar( 'Migrating', $total_posts );
		$count       = 0;
		$drupal_db   = get_database();
		WP_CLI::log( WP_CLI::colorize( '%GFound posts: %n' . $total_posts ) );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Prepare for migration.
		prepare_for_migration();

		// Migrate permalink redirects.
		foreach ( $posts as $post ) {
			// Check for type.
			if ( ! empty( $post['drupal_id'] ) ) {
				$id   = absint( $post['drupal_id'] );
				$type = 'node';
			} else {
				$progress->tick();
				continue;
			}

			// Get WordPress URL.
			$wordpress_url = get_permalink( $post['ID'] );

			// Check if we have a URL.
			if ( empty( $wordpress_url ) ) {
				$progress->tick();
				continue;
			}

			// Also look for redirects if it's a draft post.
			if ( 'draft' === $post['post_status'] ) {
				$drupal_redirect_url = $drupal_db->get_row(
					$drupal_db->prepare(
						'
							SELECT
								redirect.redirect_redirect__uri AS drupal_url
							FROM
								redirect
							WHERE
								redirect.redirect_source__path = %s
							',
						[
							$type . '/' . $id,
						]
					),
					ARRAY_A
				);

				// Check if we have a redirect.
				if ( is_array( $drupal_redirect_url ) && ! empty( $drupal_redirect_url['drupal_url'] ) ) {
					// A redirect was found for this draft post.
					// Change our WP URL to that URL.
					$new_wp_url = get_wp_permalink( $drupal_redirect_url['drupal_url'] );

					// Check if we have a new URL.
					if ( $new_wp_url !== $drupal_redirect_url['drupal_url'] ) {
						$wordpress_url = $new_wp_url;
					}
				}
			}

			// Parse URL.
			$wordpress_url = wp_parse_url( $wordpress_url );

			// Check if we have a path.
			if ( ! is_array( $wordpress_url ) || empty( $wordpress_url['path'] ) ) {
				$progress->tick();
				continue;
			}

			// Clean URL.
			$wordpress_url = $wordpress_url['path'];

			// Get Drupal URLs.
			$drupal_urls = $drupal_db->get_results(
				$drupal_db->prepare(
					'
					SELECT
						path_alias.alias AS drupal_url
					FROM
						path_alias
					WHERE
						path_alias.path = %s
					ORDER BY
						path_alias.id DESC
					',
					[
						'/' . $type . '/' . $id,
					]
				),
				ARRAY_A
			);

			// Check if we have Drupal URLs.
			if ( ! is_array( $drupal_urls ) || empty( $drupal_urls ) ) {
				$progress->tick();
				continue;
			}

			// Traverse Drupal URLs.
			foreach ( $drupal_urls as $drupal_url ) {
				$drupal_url = trim( $drupal_url['drupal_url'] ?? '' );

				// Skip if we have empty URL or same URL.
				if ( empty( $drupal_url ) || $drupal_url === $wordpress_url ) {
					continue;
				}

				// Make URL to be compatible with "redirection" plugin.
				$url        = new Red_Url_Match( $drupal_url );
				$drupal_url = $url->get_url();

				// Create redirect.
				$wpdb->insert(
					$wpdb->prefix . 'redirection_items',
					[
						'url'         => $drupal_url,
						'match_url'   => $drupal_url,
						'group_id'    => 3,
						'action_type' => 'url',
						'action_code' => '301',
						'action_data' => $wordpress_url,
						'match_type'  => 'url',
						'match_data'  => '{"source":{"flag_query":"pass"}}',
					]
				);

				// Update count.
				++$count;
			}

			// Update progress.
			$progress->tick();
		}

		// All done!
		$progress->finish();
		WP_CLI::success( "Added $count redirects for $total_posts posts." );
	}

	/**
	 * Migrate 301 redirects for terms new permalinks.
	 *
	 * @subcommand redirect-term-new-permalinks
	 *
	 * @return void
	 *
	 * @throws ExitException Exit on failure of command.
	 **/
	public function redirect_terms_new_permalinks(): void {
		// Welcome message.
		WP_CLI::log( WP_CLI::colorize( '%YMigrating terms permalink redirects...%n' ) );

		// Build query.
		global $wpdb;
		$query = "
		SELECT
			tt.term_id,
			tt.taxonomy,
			tm.meta_value as drupal_id
		FROM
			wp_term_taxonomy as tt
			LEFT JOIN
				wp_termmeta as tm ON tt.term_id = tm.term_id
		WHERE
			tt.taxonomy = 'category' and tm.meta_key = 'drupal_term_id'
		";

		// Get terms.
		$terms = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore

		// Check if we have terms.
		if ( empty( $terms ) ) {
			WP_CLI::error( 'No terms found!' );
		}

		// Progress bar.
		$total_terms = count( $terms );
		$progress    = make_progress_bar( 'Migrating', $total_terms );
		$count       = 0;
		$drupal_db   = get_database();
		WP_CLI::log( WP_CLI::colorize( '%GFound terms: %n' . $total_terms ) );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Prepare for migration.
		prepare_for_migration();

		// Migrate permalink redirects.
		foreach ( $terms as $term ) {
			// Check for type.
			if ( ! empty( $term['drupal_id'] ) ) {
				$id = absint( $term['drupal_id'] );
			} else {
				$progress->tick();
				continue;
			}

			// Get WordPress term URL.
			$wordpress_url = get_term_link( absint( $term['term_id'] ), $term['taxonomy'] );

			// Check if we have a URL.
			if ( empty( $wordpress_url ) || ! is_string( $wordpress_url ) ) {
				$progress->tick();
				continue;
			}

			// Parse URL.
			$wordpress_url = wp_parse_url( $wordpress_url );

			// Check if we have a path.
			if ( ! is_array( $wordpress_url ) || empty( $wordpress_url['path'] ) ) {
				$progress->tick();
				continue;
			}

			// Clean URL.
			$wordpress_url = $wordpress_url['path'];

			// Get Drupal URLs.
			$drupal_urls = $drupal_db->get_results(
				$drupal_db->prepare(
					'
					SELECT
						path_alias.alias AS drupal_url
					FROM
						path_alias
					WHERE
						path_alias.path = %s
					ORDER BY
						path_alias.id DESC
					',
					[
						'/taxonomy/term/' . $id,
					]
				),
				ARRAY_A
			);

			// Check if we have Drupal URLs.
			if ( ! is_array( $drupal_urls ) || empty( $drupal_urls ) ) {
				$progress->tick();
				continue;
			}

			// Traverse Drupal URLs.
			foreach ( $drupal_urls as $drupal_url ) {
				$drupal_url = trim( $drupal_url['drupal_url'] ?? '' );

				// Skip if we have empty URL or same URL.
				if ( empty( $drupal_url ) || $drupal_url === $wordpress_url ) {
					continue;
				}

				// Make URL to be compatible with "redirection" plugin.
				$url        = new Red_Url_Match( $drupal_url );
				$drupal_url = $url->get_url();

				// Create redirect.
				$wpdb->insert(
					$wpdb->prefix . 'redirection_items',
					[
						'url'         => $drupal_url,
						'match_url'   => $drupal_url,
						'group_id'    => 3,
						'action_type' => 'url',
						'action_code' => '301',
						'action_data' => $wordpress_url,
						'match_type'  => 'url',
						'match_data'  => '{"source":{"flag_query":"pass"}}',
					]
				);

				// Update count.
				++$count;
			}

			// Update progress.
			$progress->tick();
		}

		// All done!
		$progress->finish();
		WP_CLI::success( "Added $count redirects for $total_terms posts." );
	}

	/**
	 * Migrate 301 redirects.
	 *
	 * @subcommand url-redirects
	 *
	 * @return void
	 * @throws ExitException Exit on failure of command.
	 **/
	public function url_redirects(): void {
		// Welcome message.
		WP_CLI::log( WP_CLI::colorize( '%YMigrating URL redirects...%n' ) );

		// Build query.
		$drupal_db = get_database();
		$query     = '
		SELECT
			redirect_source__path as source,
			redirect_redirect__uri as redirect,
			status_code
		FROM
			redirect
		';

		// Get redirects.
		$redirects = $drupal_db->get_results( $query, ARRAY_A );

		// Check if we have redirects.
		if ( ! is_array( $redirects ) || empty( $redirects ) ) {
			WP_CLI::error( 'No redirects found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Get total redirects.
		$total_redirects = count( $redirects );
		$progress        = make_progress_bar( 'Migrating', $total_redirects );
		$count           = 0;
		$home_url        = trailingslashit( home_url() );
		WP_CLI::log( WP_CLI::colorize( '%GFound redirects: %n' . $total_redirects ) );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Prepare for migration.
		prepare_for_migration();

		// Migrate redirects.
		foreach ( $redirects as $redirect ) {
			// Check if we have a redirect.
			if ( empty( $redirect['redirect'] ) ) {
				$progress->tick();
				continue;
			}

			// Clean source.
			$source = str_replace( 'internal:', '', $redirect['source'] );
			$source = '/' . ltrim( $source, '/' );

			// Look for redirect URL.
			$wp_link = get_wp_permalink( $redirect['redirect'] );

			// Clean links.
			if ( str_starts_with( $wp_link, 'http' ) ) {
				$wp_link = str_replace( 'http://', 'https://', $wp_link );
				$wp_link = str_replace( $home_url, '', $wp_link );

				// Check its link.
				if ( ! str_starts_with( $wp_link, 'http' ) ) {
					$wp_link = '/' . ltrim( $wp_link, '/' );
				}
			} else {
				$wp_link = '/' . ltrim( $wp_link, '/' );
			}

			// Skip if wp-link is same as source.
			if ( $wp_link === $source ) {
				$progress->tick();
				continue;
			}

			// Make URL to be compatible with "redirection" plugin.
			$url    = new Red_Url_Match( $source );
			$source = $url->get_url();

			// Skip if wp-link starts with `/node/`.
			// These nodes are - departures, agents, agents-updates, photographic-journal.
			if ( str_starts_with( $wp_link, '/node/' ) ) {
				$progress->tick();
				continue;
			}

			// Globalize WPDB.
			global $wpdb;

			// Create redirect.
			$wpdb->insert(
				$wpdb->prefix . 'redirection_items',
				[
					'url'         => $source,
					'match_url'   => $source,
					'group_id'    => 4,
					'action_type' => 'url',
					'action_code' => '301',
					'action_data' => $wp_link,
					'match_type'  => 'url',
					'match_data'  => '{"source":{"flag_query":"pass"}}',
				]
			);

			// Update progress.
			++$count;
			$progress->tick();
		}

		// All done!
		$progress->finish();
		WP_CLI::success( "Migrated $count out of $total_redirects." );
	}

	/**
	 * Update internal links.
	 *
	 * @param mixed[] $args       WP CLI arguments.
	 * @param mixed[] $args_assoc WP CLI associative arguments.
	 *
	 * @subcommand update-internal-links
	 * @synopsis [--ids=<1,2>]
	 *
	 * @throws ExitException Exception on error.
	 *
	 * @return void
	 **/
	public function update_internal_links( array $args = [], array $args_assoc = [] ): void {
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
		WP_CLI::log( WP_CLI::colorize( '%YMigrating meta tags...%n' ) );

		// Build query.
		global $wpdb;
		$query = "
		SELECT
			p.post_type,
			p.ID,
			p.post_content
		FROM
			wp_posts AS p
		WHERE
			p.post_type IN ( 'post', 'qrk_press_release', 'qrk_adventure_option', 'qrk_cabin_category', 'qrk_expedition', 'qrk_inclusion_set', 'qrk_exclusion_set', 'qrk_itinerary', 'qrk_itinerary_day', 'qrk_agreement', 'qrk_pre_post_trip', 'qrk_region', 'qrk_ship', 'qrk_ship_deck', 'qrk_staff_member' )
		";

		// Add IDs if provided.
		if ( ! empty( $options['ids'] ) ) {
			$query .= ' AND p.ID IN (' . implode( ',', $options['ids'] ) . ')';
		}

		// Get posts.
		$posts = $wpdb->get_results( $query, ARRAY_A ); // phpcs:ignore

		// Check if we have posts.
		if ( empty( $posts ) ) {
			WP_CLI::error( 'No posts found!' );
		}

		// Progress bar.
		$total_posts = count( $posts );
		$progress    = make_progress_bar( 'Updating', $total_posts );
		$count       = 0;
		WP_CLI::log( WP_CLI::colorize( '%GFound posts: %n' . $total_posts ) );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}

		// Prepare for migration.
		prepare_for_migration();

		// Migrate posts.
		foreach ( $posts as $post ) {
			// Post content.
			$new_content = $this->replace_internal_links( $post['post_content'] ?? '' );

			// Update post content.
			if ( $new_content !== $post['post_content'] ) {
				wp_update_post(
					[
						'ID'           => $post['ID'],
						'post_content' => $new_content,
					]
				);
			}

			// Post meta.
			$meta = get_post_meta( $post['ID'] );

			// Update post meta.
			if ( ! empty( $meta ) && is_array( $meta ) ) {
				// Loop through meta.
				foreach ( $meta as $meta_key => $meta_value ) {
					// Skip empty values.
					if ( empty( $meta_value[0] ) ) {
						continue;
					}

					// Replace internal links.
					$new_value = $this->replace_internal_links( $meta_value[0] );

					// Update meta.
					if ( $new_value !== $meta_value[0] ) {
						update_post_meta( $post['ID'], $meta_key, $new_value );
					}
				}
			}

			// Update progress.
			++$count;
			$progress->tick();
		}

		// All done!
		$progress->finish();
		WP_CLI::success( "Updated $count out of $total_posts." );
	}

	/**
	 * Replace internal links.
	 *
	 * @param string $content Original content.
	 *
	 * @return string
	 */
	public function replace_internal_links( string $content = '' ): string {
		// Get WPDB object.
		global $wpdb;
		$drupal_db = get_database();

		// URLs.
		preg_match_all( '/<a\s+(?:[^>]*?\s+)?href=([\"\'])(.*?)\1/', $content, $matches );

		// Check if we have any matches.
		if ( ! empty( $matches[2] ) ) {
			foreach ( $matches[2] as $index => $url ) {
				// Temp URL.
				$temp_url = $url;

				// Check if it's a relative URL.
				if ( str_starts_with( $url, '/' ) ) {
					$temp_url = sprintf( 'https://www.quarkexpeditions.com%s', $url );
				}

				// Skip external links.
				if ( str_starts_with( $temp_url, 'http' ) && ! str_contains( $temp_url, 'quarkexpeditions.com' ) ) {
					continue;
				}

				// Parse URL.
				$parsed_url = wp_parse_url( $temp_url );

				// Check if we have a path.
				if ( ! is_array( $parsed_url ) || empty( $parsed_url['path'] ) ) {
					continue;
				}

				// Get existing redirect.
				$parsed_path       = '/' . ltrim( $parsed_url['path'], '/' );
				$existing_redirect = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT * FROM {$wpdb->prefix}redirection_items WHERE url = %s",
						[
							$parsed_path,
						]
					),
					ARRAY_A
				);

				// Check if we have an existing redirect.
				if ( ! empty( $existing_redirect ) ) {
					$new_url = str_replace( $url, $existing_redirect['action_data'], $matches[0][ $index ] );
					$content = str_replace( $matches[0][ $index ], $new_url, $content );
				}
			}
		}

		// Node links.
		// Get ready for this!
		preg_match_all( '#node/([0-9]+)#', $content, $matches );

		// Check if we have any matches.
		if ( ! empty( $matches[0] ) ) {
			// Loop through links.
			foreach ( $matches[0] as $key => $drupal_link ) {
				if ( ! empty( $matches[1][ $key ] ) ) {
					// Try to get a WP link.
					$wp_link = get_wp_permalink( $drupal_link );

					// Check if we didn't find one.
					if ( $wp_link === $drupal_link ) {
						// Fallback to redirect.
						$fallback_redirect = $wpdb->get_row(
							$wpdb->prepare(
								"
								SELECT
									*
								FROM
									{$wpdb->prefix}redirection_items
								WHERE
									`url` = %s
								ORDER BY
								    id DESC
								LIMIT 1
								",
								[
									'/' . $drupal_link,
								]
							),
							ARRAY_A
						);

						// Check if we have a fallback redirect.
						if ( ! empty( $fallback_redirect['action_data'] ) ) {
							// Try to determine the post based on redirect.
							$wp_link_post_id = url_to_postid( $fallback_redirect['action_data'] );

							// Check if we have a post ID.
							if ( ! empty( $wp_link_post_id ) ) {
								$wp_link = home_url( 'post/' . $wp_link_post_id );
							} else {
								$wp_link = home_url( $fallback_redirect['action_data'] );
							}
						}
					}

					// Check if we have a URL.
					if ( ! empty( $wp_link ) ) {
						$content = str_replace( $drupal_link, $wp_link, $content );
					}
				}
			}
		}

		// Files.
		preg_match_all( '#https://www.quarkexpeditions.com/sites/default/files/(.+?)\.([a-zA-Z0-9]){3,4}#', $content, $matches );

		// Check if we have any matches.
		if ( ! empty( $matches[0] ) ) {
			foreach ( $matches[0] as $key => $drupal_url ) {
				if ( empty( $matches[1][ $key ] ) ) {
					continue;
				}

				// Get file.
				$drupal_file = $drupal_db->get_row(
					$drupal_db->prepare(
						'
						SELECT
							fid AS drupal_id
						FROM
						    file_managed
						WHERE
						    uri LIKE %s
						LIMIT 1
						',
						[
							'%' . str_replace( 'https://www.quarkexpeditions.com/sites/default/files/', '', $drupal_url ) . '%',
						]
					),
					ARRAY_A
				);

				// Check if we have a file id.
				if ( ! is_array( $drupal_file ) || empty( $drupal_file['drupal_id'] ) ) {
					continue;
				}

				// Download file.
				$wp_file = download_file_by_fid( absint( $drupal_file['drupal_id'] ) );

				// Check if we have a file.
				if ( is_int( $wp_file ) ) {
					$wp_url = wp_get_attachment_url( $wp_file );

					// Check if we have a URL.
					if ( ! empty( $wp_url ) ) {
						$content = str_replace( $drupal_url, $wp_url, $content );
					}
				}
			}
		}

		// All done.
		return $content;
	}
}
