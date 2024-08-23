<?php
/**
 * Namespace for Manual Sync
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\ManualSync;

use WP_Admin_Bar;
use WP_Screen;

use function Quark\Softrip\do_sync;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;

/**
 * Bootstrap.
 *
 * @return void
 */
function bootstrap(): void {
	if ( is_admin() ) {
		add_action( 'admin_bar_menu', __NAMESPACE__ . '\\create_admin_bar_menus', 100 );
		add_action( 'admin_footer', __NAMESPACE__ . '\\admin_footer_actions' );
		add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_scripts' );
		add_action( 'admin_action_sync', __NAMESPACE__ . '\\manually_synchronize' );
		add_action( 'admin_notices', __NAMESPACE__ . '\\show_sync_admin_notice' );
	}
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
 * Print admin footer JS.
 *
 * @return void
 */
function admin_footer_actions(): void {
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
	<template id="quark-softrip-admin-bar-sync-button">
		<a href="<?php echo esc_url( get_sync_admin_url( absint( get_the_ID() ) ) ); ?>" class="components-button is-secondary is-compact">
			<span class="ab-icon dashicons-before dashicons-update">
				<?php esc_html_e( 'Sync', 'quark' ); ?>
			</span>
		</a>
	</template>
	<?php
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
		'quark-softrip-sync-admin',
		plugin_dir_url( __DIR__ ) . '../dist/quark-softrip-manual-sync.js',
		[],
		strval( filemtime( dirname( __DIR__ ) . '/../dist/quark-softrip-manual-sync.js' ) ),
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
		'quark-softrip-sync-admin',
		'quarkSoftripAdmin',
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
				$is_success          = do_sync( $related_itineraries, [] );
			}
			break;

		// Sync specific departures.
		case DEPARTURE_POST_TYPE:
			$is_success = do_sync( [], [ $post_id ] );
			break;

		// Sync itinerary.
		case ITINERARY_POST_TYPE:
			$is_success = do_sync( [ $post_id ], [] );
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
