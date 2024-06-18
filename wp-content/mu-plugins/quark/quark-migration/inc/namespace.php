<?php
/**
 * Namespace functions.
 *
 * @package quark-migration
 */

namespace Quark\Migration;

use WP_CLI;
use WP_Screen;

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// Hooks.
	add_action( 'add_meta_boxes', __NAMESPACE__ . '\\add_drupal_migration_meta_box' );

	// CLI commands.
	if ( defined( 'WP_CLI' ) && true === WP_CLI ) {
		WP_CLI::add_command( 'quark-migrate media', __NAMESPACE__ . '\\WP_CLI\\Media' );
		WP_CLI::add_command( 'quark-migrate press-release', __NAMESPACE__ . '\\WP_CLI\\Press_Release' );
		WP_CLI::add_command( 'quark-migrate blog', __NAMESPACE__ . '\\WP_CLI\\Blog' );
		WP_CLI::add_command( 'quark-migrate port', __NAMESPACE__ . '\\WP_CLI\\Port' );
		WP_CLI::add_command( 'quark-migrate ship-deck', __NAMESPACE__ . '\\WP_CLI\\Ship_Deck' );
		WP_CLI::add_command( 'quark-migrate taxonomy', __NAMESPACE__ . '\\WP_CLI\\Taxonomies' );
		WP_CLI::add_command( 'quark-migrate ship', __NAMESPACE__ . '\\WP_CLI\\Ship' );
		WP_CLI::add_command( 'quark-migrate post-trip-options', __NAMESPACE__ . '\\WP_CLI\\Pre_Post_Trip_Options' );
		WP_CLI::add_command( 'quark-migrate cabin-category', __NAMESPACE__ . '\\WP_CLI\\Cabin_Category' );
		WP_CLI::add_command( 'quark-migrate inclusion-exclusion-sets', __NAMESPACE__ . '\\WP_CLI\\Inclusion_Exclusion_Set' );
		WP_CLI::add_command( 'quark-migrate itinerary-day', __NAMESPACE__ . '\\WP_CLI\\Itinerary_Day' );
		WP_CLI::add_command( 'quark-migrate itinerary', __NAMESPACE__ . '\\WP_CLI\\Itinerary' );
		WP_CLI::add_command( 'quark-migrate region', __NAMESPACE__ . '\\WP_CLI\\Region_Landing_Page' );
		WP_CLI::add_command( 'quark-migrate staff-member', __NAMESPACE__ . '\\WP_CLI\\Staff_Member' );
		WP_CLI::add_command( 'quark-migrate offers', __NAMESPACE__ . '\\WP_CLI\\Offer' );
	}
}

/**
 * Add Drupal Migration meta box.
 *
 * @return void
 */
function add_drupal_migration_meta_box(): void {
	// Add to certain post types only.
	$post_types = get_post_types();

	// Get current screen detail.
	$screen = get_current_screen();

	// If it's not post type screen then bail out.
	if ( ! $screen instanceof WP_Screen || empty( $screen->post_type ) || ! in_array( $screen->post_type, $post_types, true ) ) {
		return;
	}

	// Get Drupal ID.
	$drupal_id = absint( get_post_meta( absint( get_the_ID() ), 'drupal_id', true ) );

	// If Drupal ID is not exists then bail out.
	if ( empty( $drupal_id ) ) {
		return;
	}

	// Add meta box.
	add_meta_box(
		'drupal-migration',
		'Drupal Migration',
		function () use ( $drupal_id ) {
			$drupal_url = 'https://dev.quarkexpeditions.com/';
			?>
			<p>Drupal Node ID: <code><?php echo absint( $drupal_id ); ?></code></p>
			<a href="<?php echo esc_url( sprintf( '%s/node/%d', $drupal_url, $drupal_id ) ); ?>" class="button button-secondary" target="_blank">View On Drupal</a>
			&nbsp;
			<a href="<?php echo esc_url( sprintf( '%s/node/%d/edit', $drupal_url, $drupal_id ) ); ?>" class="button button-primary" target="_blank">Edit In Drupal</a>
			<?php
		},
		$post_types,
		'side',
		'low'
	);
}

/**
 * Write to log file.
 *
 * @param string $message Log message.
 *
 * @return void
 */
function log_warning( string $message = '' ): void {
	// Get the log file path.
	$filename  = trailingslashit( WP_CONTENT_DIR ) . 'uploads/tmp-migration.log';
	$timestamp = gmdate( 'Y-m-d H:i:s' );
	$log_entry = "[$timestamp] $message\n";

	// Write the log entry to the file.
	error_log( $log_entry, 3, $filename ); // PHPCS:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
}
