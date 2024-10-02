<?php
/**
 * Namespace functions for urgent/critical push functionality.
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor\Urgent;

use WP_Error;
use WP_Post;

use function Quark\CabinCategories\get as get_cabin_post;
use function Quark\Core\get_raw_text_from_html;
use function Quark\Expeditions\get as get_expedition_post;
use function Quark\Ingestor\do_push;
use function Quark\Softrip\AdventureOptions\get_departures_by_adventure_option_term_id;
use function Quark\Softrip\Occupancies\get_departures_by_cabin_category_id;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;

const URGENTLY_CHANGED_EXPEDITION_IDS_OPTION = '_urgently_changed_expedition_ids';
const URGENTLY_TRACKED_DATA_HASH_META        = '_urgently_tracked_data_hash';
const SCHEDULE_HOOK                          = 'qrk_ingestor_urgent_push';
const URGENT_INGESTOR_PUSH_EVENT_NAME        = 'urgent-ingestor-push';

/**
 * Bootstrap.
 *
 * @return void
 */
function bootstrap(): void {
	// Track expedition post type change.
	add_action( 'acf/save_post', __NAMESPACE__ . '\\track_expedition_post_type_change', 999 );

	// Track cabin post type change. Using ACF hook as ACF fields are also tracked.
	add_action( 'acf/save_post', __NAMESPACE__ . '\\track_cabin_post_type_change', 999 );

	// Track adventure option taxonomy change.
	add_action( 'saved_' . ADVENTURE_OPTION_CATEGORY, __NAMESPACE__ . '\\track_adventure_option_taxonomy_change', 999 );
}

/**
 * Track expedition post type change.
 *
 * @param int|string $post_id Post ID.
 *
 * @return void
 */
function track_expedition_post_type_change( int|string $post_id = 0 ): void {
	// Bail if post id is not integer.
	if ( is_string( $post_id ) ) {
		return;
	}

	// Bail autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Bail if post is revision.
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	// Bail if post is autosave.
	if ( wp_is_post_autosave( $post_id ) ) {
		return;
	}

	// Get expedition post.
	$expedition_post = get_expedition_post( $post_id );

	// Bail if expedition post is not found.
	if ( empty( $expedition_post['post'] ) || ! $expedition_post['post'] instanceof WP_Post ) {
		return;
	}

	// Get title.
	$title = get_raw_text_from_html( $expedition_post['post']->post_title );

	// Initialize image ids.
	$image_ids = [];

	// Check if slider images are set.
	if ( ! empty( $expedition_post['data'] ) && ! empty( $expedition_post['data']['hero_card_slider_image_ids'] ) && is_array( $expedition_post['data']['hero_card_slider_image_ids'] ) ) {
		$image_ids = array_map( 'absint', $expedition_post['data']['hero_card_slider_image_ids'] );
	}

	// Tracked data.
	$tracked_data = [
		'title'     => $title,
		'image_ids' => $image_ids,
	];

	// Json data.
	$json_data = wp_json_encode( $tracked_data );

	// Validate json data.
	if ( empty( $json_data ) ) {
		return;
	}

	// Get hash.
	$hash = md5( $json_data );

	// Get stored hash.
	$stored_hash = get_post_meta( $post_id, URGENTLY_TRACKED_DATA_HASH_META, true );

	// Check if hash is different.
	if ( $hash === $stored_hash ) {
		return;
	}

	// Get changed expedition ids from options.
	$changed_expedition_ids = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION, [] );

	// Validate changed expedition ids.
	if ( ! is_array( $changed_expedition_ids ) ) {
		$changed_expedition_ids = [];
	}

	// Add if not exists.
	if ( ! in_array( $post_id, $changed_expedition_ids, true ) ) {
		$changed_expedition_ids[] = $post_id;

		// Update changed expedition ids.
		update_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION, $changed_expedition_ids, false );
	}

	// Update stored hash.
	update_post_meta( $post_id, URGENTLY_TRACKED_DATA_HASH_META, $hash );

	// Schedule urgent push.
	dispatch_urgent_push_gh_event( $changed_expedition_ids );
}

/**
 * Track cabin post type change.
 *
 * @param int|string $post_id Post ID.
 *
 * @return void
 */
function track_cabin_post_type_change( int|string $post_id = 0 ): void {
	// Bail if post id is not integer.
	if ( is_string( $post_id ) ) {
		return;
	}

	// Bail autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Bail if post is revision.
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	// Bail if post is autosave.
	if ( wp_is_post_autosave( $post_id ) ) {
		return;
	}

	// Get cabin post.
	$cabin_post = get_cabin_post( $post_id );

	// Validate cabin post.
	if ( empty( $cabin_post['post'] ) || ! $cabin_post['post'] instanceof WP_Post ) {
		return;
	}

	// Get title.
	$title = get_raw_text_from_html( $cabin_post['post']->post_title );

	// Get description.
	$description = get_raw_text_from_html( $cabin_post['post']->post_content );

	// Initialize image ids.
	$image_ids = [];

	// Check if gallery images are set.
	if ( ! empty( $cabin_post['post_meta'] ) && ! empty( $cabin_post['post_meta']['cabin_images'] ) && is_array( $cabin_post['post_meta']['cabin_images'] ) ) {
		$image_ids = array_map( 'absint', $cabin_post['post_meta']['cabin_images'] );
	}

	// Tracked data.
	$tracked_data = [
		'title'       => $title,
		'description' => $description,
		'image_ids'   => $image_ids,
	];

	// Json data.
	$json_data = wp_json_encode( $tracked_data );

	// Validate json data.
	if ( empty( $json_data ) ) {
		return;
	}

	// Get hash.
	$hash = md5( $json_data );

	// Get stored hash.
	$stored_hash = get_post_meta( $post_id, URGENTLY_TRACKED_DATA_HASH_META, true );

	// Check if hash is different.
	if ( $hash === $stored_hash ) {
		return;
	}

	// Get departures of this cabin.
	$departure_post_ids = get_departures_by_cabin_category_id( $post_id );

	// Validate departure post ids.
	if ( empty( $departure_post_ids ) || ! is_array( $departure_post_ids ) ) {
		return;
	}

	// Initialize changed expedition ids.
	$changed_expedition_ids = [];

	// Loop through departure post ids.
	foreach ( $departure_post_ids as $departure_post_id ) {
		// Validate departure post id.
		if ( empty( $departure_post_id ) ) {
			continue;
		}

		// Get expedition post id.
		$expedition_post_id = absint( get_post_meta( $departure_post_id, 'related_expedition', true ) );

		// Validate expedition post id.
		if ( empty( $expedition_post_id ) ) {
			continue;
		}

		// Insert if not exists.
		if ( ! in_array( $expedition_post_id, $changed_expedition_ids, true ) ) {
			$changed_expedition_ids[] = $expedition_post_id;
		}
	}

	// Remove duplicates.
	$changed_expedition_ids = array_unique( $changed_expedition_ids );

	// Validate changed expedition ids.
	if ( empty( $changed_expedition_ids ) ) {
		return;
	}

	// Get changed expedition ids from options.
	$stored_changed_expedition_ids = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION, [] );

	// Validate stored changed expedition ids.
	if ( ! is_array( $stored_changed_expedition_ids ) ) {
		$stored_changed_expedition_ids = [];
	}

	// Merge.
	$changed_expedition_ids = array_merge( $changed_expedition_ids, $stored_changed_expedition_ids );

	// Remove duplicates.
	$changed_expedition_ids = array_unique( $changed_expedition_ids );

	// Update changed expedition ids.
	update_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION, $changed_expedition_ids, false );

	// Update stored hash.
	update_post_meta( $post_id, URGENTLY_TRACKED_DATA_HASH_META, $hash );

	// Schedule urgent push.
	dispatch_urgent_push_gh_event( $changed_expedition_ids );
}

/**
 * Track adventure option taxonomy change.
 *
 * @param int $term_id Term ID.
 *
 * @return void
 */
function track_adventure_option_taxonomy_change( int $term_id = 0 ): void {
	// Bail if not valid.
	if ( empty( $term_id ) ) {
		return;
	}

	// Get adventure option category.
	$adventure_option_category = get_term( $term_id, ADVENTURE_OPTION_CATEGORY, ARRAY_A );

	// Bail if adventure option category is not found.
	if ( empty( $adventure_option_category ) || ! is_array( $adventure_option_category ) ) {
		return;
	}

	// Get title.
	$title = get_raw_text_from_html( $adventure_option_category['name'] );

	// Get description.
	$description = get_raw_text_from_html( $adventure_option_category['description'] );

	// Initialize image ids.
	$image_id = 0;
	$icon_id  = 0;

	// Get image meta.
	$image_id = absint( get_term_meta( $term_id, 'image', true ) );

	// Get icon meta.
	$icon_id = absint( get_term_meta( $term_id, 'icon', true ) );

	// Tracked data.
	$tracked_data = [
		'title'       => $title,
		'description' => $description,
		'image_id'    => $image_id,
		'icon_id'     => $icon_id,
	];

	// Json data.
	$json_data = wp_json_encode( $tracked_data );

	// Validate json data.
	if ( empty( $json_data ) ) {
		return;
	}

	// Get hash.
	$hash = md5( $json_data );

	// Get stored hash.
	$stored_hash = get_term_meta( $term_id, URGENTLY_TRACKED_DATA_HASH_META, true );

	// Check if hash is different.
	if ( $hash === $stored_hash ) {
		return;
	}

	// Get departures by adventure option category.
	$departure_post_ids = get_departures_by_adventure_option_term_id( $term_id );

	// Validate departure post ids.
	if ( empty( $departure_post_ids ) || ! is_array( $departure_post_ids ) ) {
		return;
	}

	// Initialize changed expedition ids.
	$changed_expedition_ids = [];

	// Loop through departure post ids.
	foreach ( $departure_post_ids as $departure_post_id ) {
		// Validate departure post id.
		if ( empty( $departure_post_id ) ) {
			continue;
		}

		// Get expedition post id.
		$expedition_post_id = absint( get_post_meta( $departure_post_id, 'related_expedition', true ) );

		// Validate expedition post id.
		if ( empty( $expedition_post_id ) ) {
			continue;
		}

		// Insert if not exists.
		if ( ! in_array( $expedition_post_id, $changed_expedition_ids, true ) ) {
			$changed_expedition_ids[] = $expedition_post_id;
		}
	}

	// Remove duplicates.
	$changed_expedition_ids = array_unique( $changed_expedition_ids );

	// Validate changed expedition ids.
	if ( empty( $changed_expedition_ids ) ) {
		return;
	}

	// Get changed expedition ids from options.
	$stored_changed_expedition_ids = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION, [] );

	// Validate stored changed expedition ids.
	if ( ! is_array( $stored_changed_expedition_ids ) ) {
		$stored_changed_expedition_ids = [];
	}

	// Merge.
	$changed_expedition_ids = array_merge( $changed_expedition_ids, $stored_changed_expedition_ids );

	// Remove duplicates.
	$changed_expedition_ids = array_unique( $changed_expedition_ids );

	// Update changed expedition ids.
	update_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION, $changed_expedition_ids, false );

	// Update stored hash.
	update_term_meta( $term_id, URGENTLY_TRACKED_DATA_HASH_META, $hash );

	// Schedule urgent push.
	dispatch_urgent_push_gh_event( $changed_expedition_ids );
}

/**
 * Dispatch urgent push event to Github.
 *
 * @param int[] $expedition_ids Expedition IDs.
 *
 * @return bool
 */
function dispatch_urgent_push_gh_event( array $expedition_ids = [] ): bool {
	// Validate expedition ids.
	if ( empty( $expedition_ids ) ) {
		return false;
	}

	// Check credentials.
	if (
		! defined( 'QUARK_GITHUB_ACTIONS_TOKEN' ) ||
		! defined( 'QUARK_GITHUB_API_DISPATCH_URL' )
	) {
		// Log error.
		do_action(
			'quark_ingestor_dispatch_gh_event',
			[
				'error'          => 'Github credentials missing',
				'expedition_ids' => $expedition_ids,
			]
		);

		// Bail.
		return false;
	}

	// Set request args.
	$args = [
		'method'  => 'POST',
		'timeout' => 20,
		'headers' => [
			'Authorization' => 'Bearer ' . QUARK_GITHUB_ACTIONS_TOKEN,
		],
		'body'    => wp_json_encode(
			[
				'event_type' => URGENT_INGESTOR_PUSH_EVENT_NAME,
			]
		),
	];

	// Do request.
	$request = wp_remote_request( QUARK_GITHUB_API_DISPATCH_URL, $args );

	// Bail if failed.
	if ( $request instanceof WP_Error ) {
		// Log error.
		do_action(
			'quark_ingestor_dispatch_gh_event',
			[
				'error'          => $request->get_error_message(),
				'expedition_ids' => $expedition_ids,
			]
		);

		// Bail.
		return false;
	}

	// Check response code.
	if ( 204 !== wp_remote_retrieve_response_code( $request ) ) {
		// Log error.
		do_action(
			'quark_ingestor_dispatch_gh_event',
			[
				'error'          => wp_remote_retrieve_response_message( $request ),
				'expedition_ids' => $expedition_ids,
			]
		);

		// Bail.
		return false;
	}

	// Log success.
	do_action(
		'quark_ingestor_dispatch_gh_event',
		[
			'success'        => 'Github event dispatched',
			'expedition_ids' => $expedition_ids,
		]
	);

	// Return.
	return true;
}

/**
 * Push urgent data.
 * To avoid race-conditions, it always reads the changed expedition ids from options and resets it.
 * So, a queue based approach is implemented where in one iteration, it pushes current batch of changed expedition ids.
 * In next iteration, it pushes next batch of changed expedition ids.
 * If next batch is empty, it stops pushing and ends the loop.
 *
 * @return void
 */
function push_urgent_data(): void {
	// Push changed expedition ids.
	do {
		// Delete changed expedition ids cache in order to read latest from DB.
		wp_cache_delete( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION, 'options' );

		// Get changed expedition ids.
		$changed_expedition_ids = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION, [] );

		// Validate changed expedition ids.
		if ( empty( $changed_expedition_ids ) || ! is_array( $changed_expedition_ids ) ) {
			break;
		}

		// Reset changed expedition ids.
		update_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION, [], false );

		// Push current changed expedition ids.
		do_push( $changed_expedition_ids );
	} while ( true );
}
