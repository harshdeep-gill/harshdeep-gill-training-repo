<?php
/**
 * Namespace functions.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use cli\progress\Bar;
use WP_Admin_Bar;
use WP_CLI;
use WP_Error;
use WP_Query;
use WP_Screen;

use function Quark\Softrip\Departures\update_departures;
use function Quark\Softrip\AdventureOptions\get_table_sql as get_adventure_options_table_sql;
use function Quark\Softrip\Occupancies\get_table_sql as get_occupancies_table_sql;
use function Quark\Softrip\Promotions\get_table_sql as get_promotions_table_sql;
use function Quark\Softrip\OccupancyPromotions\get_table_sql as get_occupancy_promotions_table_sql;
use function Quark\Softrip\AdventureOptions\get_table_name as get_adventure_options_table_name;
use function Quark\Softrip\Occupancies\get_table_name as get_occupancies_table_name;
use function Quark\Softrip\OccupancyPromotions\get_table_name as get_occupancy_promotions_table_name;
use function Quark\Softrip\Promotions\get_table_name as get_promotions_table_name;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;

const SCHEDULE_RECURRENCE       = 'qrk_softrip_4_hourly';
const SCHEDULE_HOOK             = 'qrk_softrip_sync';
const ITINERARY_SYNC_BATCH_SIZE = 5;
const TABLE_PREFIX_NAME         = 'qrk_';

/**
 * Bootstrap plugin.
 *
 * @return void
 */
function bootstrap(): void {
	// CLI commands.
	if ( defined( 'WP_CLI' ) && true === WP_CLI ) {
		WP_CLI::add_command( 'quark-softrip db', __NAMESPACE__ . '\\WP_CLI\\DB' );
		WP_CLI::add_command( 'quark-softrip sync', __NAMESPACE__ . '\\WP_CLI\\Sync' );
	}

	// Add in filter to add in our sync schedule.
	add_filter( 'cron_schedules', __NAMESPACE__ . '\\cron_add_schedule' ); // phpcs:ignore WordPress.WP.CronInterval -- Verified > 4 Hour.

	// Schedule our sync task.
	add_filter( 'init', __NAMESPACE__ . '\\cron_schedule_sync' );

	// Register our sync hook.
	add_action( SCHEDULE_HOOK, __NAMESPACE__ . '\\do_sync' );

	// Register Stream log connector.
	add_filter( 'wp_stream_connectors', __NAMESPACE__ . '\\setup_stream_connectors' );

	// Register admin bar menus.
	if ( is_admin() ) {
		add_action( 'admin_bar_menu', __NAMESPACE__ . '\\create_admin_bar_menus', 100 );
		add_action( 'admin_footer', __NAMESPACE__ . '\\print_admin_footer_js' );
		add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_scripts' );
		add_action( 'admin_action_sync', __NAMESPACE__ . '\\manually_synchronize' );
		add_action( 'admin_notices', __NAMESPACE__ . '\\show_sync_admin_notice' );
	}
}

/**
 * Get custom DB table creation mapping array.
 *
 * @return array<string, string>
 */
function get_custom_db_table_mapping(): array {
	// Table names.
	$table_names = [
		get_adventure_options_table_name(),
		get_promotions_table_name(),
		get_occupancies_table_name(),
		get_occupancy_promotions_table_name(),
	];

	// Table SQL statements.
	$table_sql_statements = [
		get_adventure_options_table_sql(),
		get_promotions_table_sql(),
		get_occupancies_table_sql(),
		get_occupancy_promotions_table_sql(),
	];

	// Return list of tables used.
	return array_combine( $table_names, $table_sql_statements );
}

/**
 * Create DB table.
 *
 * @return void
 */
function create_custom_db_tables(): void {
	// Get mapping.
	$tables = get_custom_db_table_mapping();

	// Is in CLI.
	$is_in_cli = defined( 'WP_CLI' ) && true === WP_CLI;

	// Require upgrade.php.
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	// Initialize progress bar.
	$progress = null;

	// Check if in CLI.
	if ( ! empty( $is_in_cli ) ) {
		// Initialize progress bar.
		$progress = new Bar( 'Setting up tables', count( $tables ), 100 );

		// Check if progress bar exists or not.
		if ( ! $progress instanceof Bar ) {
			WP_CLI::error( 'Progress bar not found!' );

			// Bail out if progress bar not exists.
			return;
		}
	}

	// Start table creation.
	foreach ( $tables as $table_name => $sql ) {
		// Create table.
		maybe_create_table( $table_name, $sql );

		// Update progress bar.
		if ( ! empty( $progress ) ) {
			$progress->tick();
		}
	}

	// End progress bar.
	if ( ! empty( $is_in_cli ) ) {
		$progress->finish();
	}
}

/**
 * Registers our custom schedule.
 *
 * @param array<string, array<int|string, int|string>> $schedules The current schedules to add to.
 *
 * @return array<string, array<int|string, int|string>>
 */
function cron_add_schedule( array $schedules = [] ): array {
	// Explicitly define the interval in seconds.
	$interval = 4 * HOUR_IN_SECONDS; // 4 hours.

	// Create our schedule.
	$schedules[ SCHEDULE_RECURRENCE ] = [
		'interval' => $interval,
		'display'  => 'Once every 4 hours',
	];

	// return with custom added.
	return $schedules;
}

/**
 * Check if the cron task is already scheduled.
 *
 * @return bool
 */
function cron_is_scheduled(): bool {
	// Check if the schedule exists or not.
	return ! empty( wp_next_scheduled( SCHEDULE_HOOK ) );
}

/**
 * Schedule the sync cron task.
 *
 * @return void
 */
function cron_schedule_sync(): void {
	// Check if scheduled.
	if ( cron_is_scheduled() ) {
		return;
	}

	// Set a time + 4 hours.
	$next_time = time() + ( HOUR_IN_SECONDS * 4 );

	// Schedule the event. in 4 hours time.
	wp_schedule_event( $next_time, SCHEDULE_RECURRENCE, SCHEDULE_HOOK );
}

/**
 * Do the sync.
 *
 * @param int[] $itinerary_post_ids Itinerary post IDs.
 * @param int[] $specific_departure_post_ids  Departure post IDs. Default is empty array. Meant for specific departures.
 * @param bool  $unpublish_expired_departures Unpublish expired departures. Default is true.
 *
 * @return bool.
 */
function do_sync( array $itinerary_post_ids = [], array $specific_departure_post_ids = [], bool $unpublish_expired_departures = true ): bool {
	// If no itinerary post IDs are provided, get all itinerary post IDs.
	if ( empty( $itinerary_post_ids ) ) {
		// Get all itinerary post IDs as all departures should be updated.
		if ( empty( $specific_departure_post_ids ) ) {
			// Prepare args.
			$args = [
				'post_type'              => ITINERARY_POST_TYPE,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_term_meta_cache' => false,
				'ignore_sticky_posts'    => true,
				'post_status'            => [ 'draft', 'publish' ],
				'posts_per_page'         => -1,
			];

			// Run WP_Query.
			$query              = new WP_Query( $args );
			$itinerary_post_ids = array_map( 'absint', $query->posts );
		} else {
			// Get itinerary post IDs for the specific departure post IDs.
			$itinerary_post_ids = array_map( 'absint', array_map( 'wp_get_post_parent_id', $specific_departure_post_ids ) );
			$itinerary_post_ids = array_unique( $itinerary_post_ids );
		}
	}

	// Initialize package codes.
	$package_codes = [];

	// Initialize CLI variables.
	$is_in_cli = defined( 'WP_CLI' ) && true === WP_CLI;
	$progress  = null;

	// Get all package codes.
	foreach ( $itinerary_post_ids as $post_id ) {
		$package_code = get_post_meta( absint( $post_id ), 'softrip_package_code', true );

		// Validate package code.
		if ( ! empty( $package_code ) && is_string( $package_code ) ) {
			$package_codes[] = $package_code;
		}
	}

	// Bail if no package codes found.
	if ( empty( $package_codes ) ) {
		// Log CLI message.
		if ( $is_in_cli ) {
			WP_CLI::error( 'No package codes found' );
		}

		// Bail out.
		return false;
	}

	// Total count.
	$total = count( $package_codes );

	// Log CLI message.
	if ( $is_in_cli ) {
		// Welcome message.
		WP_CLI::log( WP_CLI::colorize( 'Syncing Itineraries...' ) );

		// Initialize progress bar.
		$progress = new Bar( 'Softrip sync', $total, 100 );
	}

	// Initiated via.
	$initiated_via = get_sync_initiated_via();

	// Log the sync initiated.
	do_action(
		'quark_softrip_sync_initiated',
		[
			'count' => $total,
			'via'   => $initiated_via,
		]
	);

	// Create batches.
	$batches = array_chunk( $package_codes, ITINERARY_SYNC_BATCH_SIZE );

	// Set up a counter for successful.
	$counter = 0;

	// Iterate over the batches.
	foreach ( $batches as $softrip_codes ) {
		// Get the raw departure data for the IDs.
		$raw_departures = synchronize_itinerary_departures( $softrip_codes );

		// Handle if an error is found.
		if ( ! is_array( $raw_departures ) || empty( $raw_departures ) ) {
			// Update progress bar.
			if ( $is_in_cli ) {
				$progress->tick( count( $softrip_codes ) );
			}

			// Skip since there was an error.
			continue;
		}

		// Process each departure.
		foreach ( $raw_departures as $softrip_package_code => $departures ) {
			// Validate is array and not empty.
			if ( ! is_string( $softrip_package_code ) || ! is_array( $departures ) || empty( $departures ) || empty( $departures['departures'] ) ) {
				// Update progress bar.
				if ( $is_in_cli ) {
					$progress->tick();
				}

				// Skip since there was an error, or departures are empty.
				continue;
			}

			// Update departure data.
			$success = update_departures( $departures['departures'], $softrip_package_code, $specific_departure_post_ids, $unpublish_expired_departures );

			// Update progress bar.
			if ( $is_in_cli ) {
				$progress->tick();
			}

			// Check if successful.
			if ( $success ) {
				// Update counter.
				++$counter;
			}
		}
	}

	// Log the sync completed.
	do_action(
		'quark_softrip_sync_completed',
		[
			'success' => $counter,
			'failed'  => $total - $counter,
			'via'     => $initiated_via,
		]
	);

	// End progress bar.
	if ( $is_in_cli ) {
		$progress->finish();

		// End notice.
		WP_CLI::success( sprintf( 'Completed %d items with %d failed items', $counter, ( $total - $counter ) ) );
	}

	// Return true if successful.
	return $counter === $total;
}

/**
 * Get sync initiated via.
 *
 * @return string
 */
function get_sync_initiated_via(): string {
	// Check if in CLI.
	if ( defined( 'WP_CLI' ) && true === WP_CLI ) {
		return 'CLI';
	}

	// Check if in cron.
	if ( defined( 'DOING_CRON' ) && true === DOING_CRON ) {
		return 'cron';
	}

	// Default to manually.
	return 'manually';
}

/**
 * Register custom stream connectors for Softrip sync.
 *
 * @param array<string, mixed> $connectors Connectors.
 *
 * @return array<string, mixed>
 */
function setup_stream_connectors( array $connectors = [] ): array {
	// Load Stream connector file.
	require_once __DIR__ . '/class-stream-connector.php';

	// Add our connector.
	$connectors['quark_softrip_sync'] = new Stream_Connector();

	// Return the connectors.
	return $connectors;
}

/**
 * Get the latest departure data from Softrip.
 *
 * @param string[] $codes Softrip codes.
 *
 * @return mixed[]|WP_Error
 */
function synchronize_itinerary_departures( array $codes = [] ): array|WP_Error {
	// Get unique codes.
	$codes = array_unique( $codes );

	// Throw error if empty.
	if ( empty( $codes ) ) {
		return new WP_Error( 'qrk_softrip_no_codes', 'No Softrip codes provided' );
	}

	// Check if the count is more than the batch size.
	if ( ITINERARY_SYNC_BATCH_SIZE < count( $codes ) ) {
		return new WP_Error( 'qrk_softrip_departures_limit', sprintf( 'The maximum number of codes allowed is %d', ITINERARY_SYNC_BATCH_SIZE ) );
	}

	// Get API adapter.
	$softrip = new Softrip_Data_Adapter();

	// Implode IDs into a string.
	$code_string = implode( ',', $codes );

	// Do request and return the result.
	return $softrip->do_request( 'departures', [ 'productCodes' => $code_string ] );
}

/**
 * Check if date is in the past.
 *
 * @param string $date Date to check.
 *
 * @return bool
 */
function is_date_in_the_past( string $date = '' ): bool {
	// Bail if empty.
	if ( empty( $date ) ) {
		return false;
	}

	// Get the current time.
	$current_time = time();

	// Get the date time.
	$date_time = absint( strtotime( $date ) );

	// If invalid date, return false.
	if ( empty( $date_time ) ) {
		return false;
	}

	// Check if expired.
	return $current_time > $date_time;
}

/**
 * Get the Table Name with prefix.
 *
 * @param string $name The table name to prefix.
 *
 * @return string
 */
function add_prefix_to_table_name( string $name = '' ): string {
	// If empty, return an empty string.
	if ( empty( $name ) ) {
		return '';
	}

	// Return the prefixed name.
	return TABLE_PREFIX_NAME . $name;
}

/**
 * Get the engine and collate.
 *
 * @return string
 */
function get_engine_collate(): string {
	// Get the $wpdb object.
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	// Set the engine and collate.
	$engine_collate = 'ENGINE=InnoDB';

	// If the charset_collate is not empty, add it to the engine_collate.
	if ( ! empty( $charset_collate ) ) {
		$engine_collate .= " $charset_collate";
	}

	// Return the engine and collate string.
	return $engine_collate;
}

/**
 * Create menus in admin bar for Softrip sync.
 * This is applicable for itinerary and departure as these have classic editor.
 *
 * @param WP_Admin_Bar|null $admin_bar Admin bar instance.
 *
 * @return void
 */
function create_admin_bar_menus( ?WP_Admin_Bar $admin_bar = null ): void {
	// Bail if null.
	if ( null === $admin_bar ) {
		return;
	}

	// Get the current screen and initialize allowed post types.
	$current_screen     = get_current_screen();
	$allowed_post_types = [
		ITINERARY_POST_TYPE,
		DEPARTURE_POST_TYPE,
	];

	// Check if the current screen is not an object of WP_Screen.
	if ( ! $current_screen instanceof WP_Screen || 'post' !== $current_screen->base ) {
		return;
	}
	$post_type = $current_screen->post_type;

	// Check if the post type is allowed.
	if ( ! in_array( $post_type, $allowed_post_types, true ) ) {
		return;
	}

	// Add the sync menu.
	$admin_bar->add_menu(
		[
			'id'     => sprintf( 'sync-%s', $post_type ),
			'parent' => null,
			'group'  => null,
			'title'  => '<span class="ab-icon dashicons-update-alt"></span>' . esc_html__( 'Sync', 'quark' ),
			'href'   => get_sync_admin_url( absint( get_the_ID() ) ),
		]
	);
}

/**
 * Print admin footer JS.
 *
 * @return void
 */
function print_admin_footer_js(): void {
	// Get the current screen.
	$current_screen = get_current_screen();

	// Check if the current screen is not an object.
	if ( ! $current_screen instanceof WP_Screen ) {
		return;
	}

	// Check if the current screen is block editor.
	if ( ! $current_screen->is_block_editor() ) {
		return;
	}

	// Check if the current screen is not a post type.
	if ( 'post' !== $current_screen->base ) {
		return;
	}

	// Get the post type.
	$post_type = $current_screen->post_type;

	// Check if the post type is not allowed.
	if ( ! in_array( $post_type, [ EXPEDITION_POST_TYPE ], true ) ) {
		return;
	}

	// Print the script.
	?>
	<script id="quark-softrip-gutenberg" type="text/html">
		<div id="quark-softrip-sync-button">
			<a href="<?php echo esc_url( get_sync_admin_url( absint( get_the_ID() ) ) ); ?>" class="components-button is-secondary is-compact">
				<span class="ab-icon dashicons-before dashicons-update">
					<?php esc_html_e( 'Sync', 'quark' ); ?>
				</span>
			</a>
		</div>
	</script>
	<?php
}

/**
 * Get sync admin URL with nonce.
 *
 * @param int $post_id Post ID.
 *
 * @return string
 */
function get_sync_admin_url( int $post_id = 0 ): string {
	// If empty post ID, return empty string.
	if ( empty( $post_id ) ) {
		return '';
	}

	// Return the URL with nonce.
	return wp_nonce_url(
		add_query_arg(
			[
				'action'  => 'sync',
				'post_id' => $post_id,
			],
			admin_url( 'admin.php?action=sync' )
		),
		'sync',
		'sync'
	);
}

/**
 * Handle redirect to post page.
 *
 * @param int                  $post_id Post ID.
 * @param array<string, mixed> $args    URL args.
 *
 * @return void
 */
function manual_sync_handle_redirect( int $post_id = 0, array $args = [] ): void {
	// If empty post ID, redirect to admin.
	if ( empty( $post_id ) ) {
		wp_safe_redirect( admin_url() );
		exit;
	}

	// Redirect to post edit page.
	wp_safe_redirect(
		add_query_arg(
			array_merge(
				[
					'post'   => $post_id,
					'action' => 'edit',
				],
				$args
			),
			admin_url( 'post.php' )
		)
	);
	exit;
}

/**
 * Enqueue admin scripts.
 *
 * @return void
 */
function enqueue_admin_scripts(): void {
	// Get the current screen.
	$current_screen = get_current_screen();

	// Check if the current screen is not an object.
	if ( ! $current_screen instanceof WP_Screen ) {
		return;
	}

	// Check if the current screen is block editor.
	if ( ! $current_screen->is_block_editor() ) {
		return;
	}

	// Check if the current screen is not a post type.
	if ( 'post' !== $current_screen->base ) {
		return;
	}

	// Get the post type.
	$post_type = $current_screen->post_type;

	// Check if the post type is not allowed.
	if ( ! in_array( $post_type, [ EXPEDITION_POST_TYPE ], true ) ) {
		return;
	}

	// Enqueue the script.
	wp_enqueue_script(
		'quark-softrip-gutenberg',
		plugin_dir_url( __DIR__ ) . 'assets/js/admin/sync/index.js',
		[],
		'1.0.0',
		true
	);

	// Get post id.
	$post_id = absint( get_the_ID() );

	// Bail if empty post ID.
	if ( empty( $post_id ) ) {
		return;
	}

	// Get the transient.
	$sync_success = get_transient( 'quark_softrip_sync_success' );

	// Validate.
	if ( ! is_array( $sync_success ) || empty( $sync_success ) ) {
		return;
	}

	// Bail if post ID not found.
	if ( ! isset( $sync_success[ $post_id ] ) ) {
		return;
	}

	// Get the success flag.
	$is_success = (bool) $sync_success[ $post_id ];

	// Prepare localization data.
	$l10n_data = [
		'message' => $is_success ? __( 'Successfully synchronized', 'quark' ) : __( 'Failed to synchronize', 'quark' ),
		'type'    => $is_success ? 'success' : 'error',
	];

	// Localize data to script.
	wp_localize_script(
		'quark-softrip-gutenberg',
		'quarkSoftripGutenberg',
		$l10n_data
	);

	// Unset the post ID.
	unset( $sync_success[ $post_id ] );

	// Delete if empty.
	if ( empty( $sync_success ) ) {
		delete_transient( 'quark_softrip_sync_success' );
	} else {
		// Update the transient.
		set_transient( 'quark_softrip_sync_success', $sync_success, 300 );
	}
}

/**
 * Manually synchronize the post.
 *
 * @return void
 */
function manually_synchronize(): void {
	// Verify nonce.
	if ( ! wp_verify_nonce( $_GET['sync'], 'sync' ) ) {
		manual_sync_handle_redirect();
	}

	// Get post ID.
	$post_id = absint( $_GET['post_id'] );

	// Bail if empty post ID.
	if ( empty( $post_id ) ) {
		return;
	}

	// Get the post type.
	$post_type = get_post_type( $post_id );

	// Check if the post type is not allowed.
	if ( ! in_array( $post_type, [ EXPEDITION_POST_TYPE, DEPARTURE_POST_TYPE, ITINERARY_POST_TYPE ], true ) ) {
		return;
	}

	// Initialize success flag.
	$is_success = false;

	// Sync the post.
	switch ( $post_type ) {
		// Sync related itineraries.
		case EXPEDITION_POST_TYPE:
			$related_itineraries = get_post_meta( $post_id, 'related_itineraries', true );

			// Validate related itineraries.
			if ( is_array( $related_itineraries ) && ! empty( $related_itineraries ) ) {
				$related_itineraries = array_map( 'absint', $related_itineraries );
				$is_success          = do_sync( $related_itineraries, [], false );
			}
			break;

		// Sync specific departures.
		case DEPARTURE_POST_TYPE:
			$is_success = do_sync( [], [ $post_id ], false );
			break;

		// Sync itinerary.
		case ITINERARY_POST_TYPE:
			$is_success = do_sync( [ $post_id ], [], false );
			break;
	}

	// Set the transient.
	set_transient(
		'quark_softrip_sync_success',
		[
			$post_id => $is_success,
		],
		300
	);

	// Handle redirect.
	manual_sync_handle_redirect( $post_id );
}

/**
 * Show admin notice on sync completion.
 *
 * @return void
 */
function show_sync_admin_notice(): void {
	// Get the current screen.
	$screen = get_current_screen();

	// Check if the current screen is not an object.
	if ( ! $screen || ! in_array( $screen->base, [ 'post' ], true ) ) {
		return;
	}

	// Check if function exists.
	if ( ! function_exists( 'wp_admin_notice' ) ) {
		return;
	}

	// Check if post is set.
	if ( ! isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	// Get the post ID.
	$post_id = absint( $_GET['post'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	// Get the post type.
	$post_type = get_post_type( $post_id );

	// Check if the post type is not allowed.
	if ( ! in_array( $post_type, [ EXPEDITION_POST_TYPE, DEPARTURE_POST_TYPE, ITINERARY_POST_TYPE ], true ) ) {
		return;
	}

	// Get the transient.
	$sync_success = get_transient( 'quark_softrip_sync_success' );

	// Validate the success flag.
	if ( ! is_array( $sync_success ) || empty( $sync_success ) ) {
		return;
	}

	// Bail if post ID not found.
	if ( ! isset( $sync_success[ $post_id ] ) ) {
		return;
	}

	// Get the success flag.
	$is_success = (bool) $sync_success[ $post_id ];

	// Get the post title.
	$post_title = get_the_title( $post_id );

	// Prepare message based on success.
	$message = $is_success ? sprintf(
		// translators: %s: Post title.
		__(
			'Successfully synchronized %s',
			'quark'
		),
		$post_title
	) : sprintf(
		// translators: %s: Post title.
		__(
			'Failed to synchronize %s',
			'quark'
		),
		$post_title
	);

	// Get the notice type.
	$notice_type = $is_success ? 'success' : 'error';

	// Show the notice.
	wp_admin_notice(
		$message,
		[
			'type'        => $notice_type,
			'dismissible' => true,
		]
	);

	// Unset the post ID.
	unset( $sync_success[ $post_id ] );

	// Delete if empty.
	if ( empty( $sync_success ) ) {
		delete_transient( 'quark_softrip_sync_success' );
	} else {
		// Update the transient.
		set_transient( 'quark_softrip_sync_success', $sync_success, 300 );
	}
}
