<?php
/**
 * Stream Connector.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_Stream\Connector;

use const Quark\Departures\POST_TYPE;

/**
 * Class Stream Connector.
 */
class Stream_Connector extends Connector {

	/**
	 * Connector slug.
	 *
	 * @var string
	 */
	public $name = 'quark_softrip_sync';

	/**
	 * Actions registered for this connector.
	 *
	 * @var string[]
	 */
	public $actions = [
		'quark_softrip_sync_initiated',
		'quark_softrip_sync_completed',
		'quark_softrip_sync_departure_updated',
		'quark_softrip_sync_departure_expired',
	];

	/**
	 * Return translated connector label.
	 *
	 * @return string
	 */
	public function get_label(): string {
		// Return label.
		return __( 'Softrip', 'qrk' );
	}

	/**
	 * Return translated context labels.
	 *
	 * @return string[]
	 */
	public function get_context_labels(): array {
		// Return labels.
		return [
			'softrip_sync' => __( 'Softrip Sync', 'qrk' ),
		];
	}

	/**
	 * Return translated action labels
	 *
	 * @return string[]
	 */
	public function get_action_labels(): array {
		// Return labels.
		return [
			'sync_initiated'         => __( 'Sync Initiated', 'qrk' ),
			'sync_completed'         => __( 'Sync Completed', 'qrk' ),
			'sync_departure_updated' => __( 'Departure Updated', 'qrk' ),
			'sync_departure_expired' => __( 'Departure Expired', 'qrk' ),
		];
	}

	/**
	 * Callback for `quark_softrip_sync_initiated` action.
	 *
	 * @param mixed[] $data Data passed to the action.
	 *
	 * @return void
	 */
	public function callback_quark_softrip_sync_initiated( array $data = [] ): void {
		// Validate data.
		if ( empty( $data ) || empty( $data['via'] ) || ! isset( $data['count'] ) ) {
			return;
		}

		// Prepare message.
		$message = sprintf(
			// translators: %1$s: Via, %2$s: Count, %3$d: Total.
			__( 'Softrip sync initiated via %1$s | Total %2$s : %3$d', 'qrk' ),
			strval( $data['via'] ),
			_n( 'itinerary', 'itineraries', absint( $data['count'] ), 'qrk' ),
			absint( $data['count'] )
		);

		// Log action.
		$this->log(
			$message,
			[],
			absint( wp_unique_id() ),
			'softrip_sync',
			'sync_initiated'
		);
	}

	/**
	 * Callback for `quark_softrip_sync_completed` action.
	 *
	 * @param mixed[] $data Data passed to the action.
	 *
	 * @return void
	 */
	public function callback_quark_softrip_sync_completed( array $data = [] ): void {
		// Validate data.
		if ( empty( $data ) || empty( $data['via'] ) || ! isset( $data['success'] ) || ! isset( $data['failed'] ) ) {
			return;
		}

		// Prepare message.
		$message = sprintf(
			// translators: %1$s: Via, %2$d: Successful, %3$d: Failed, %4$s: Successful, %5$s: Failed.
			__( 'Softrip sync completed via %1$s | Successful %4$s: %2$d | Failed %5$s: %3$d', 'qrk' ),
			strval( $data['via'] ),
			absint( $data['success'] ),
			absint( $data['failed'] ),
			_n( 'itinerary', 'itineraries', absint( $data['success'] ), 'qrk' ),
			_n( 'itinerary', 'itineraries', absint( $data['failed'] ), 'qrk' )
		);

		// Log action.
		$this->log(
			$message,
			[],
			absint( wp_unique_id() ),
			'softrip_sync',
			'sync_completed'
		);
	}

	/**
	 * Get departure update fields mapping.
	 *
	 * @return string[]
	 */
	public function get_departure_update_fields_mapping(): array {
		// Return mapping.
		return [
			'adventure_options' => __( 'Adventure Options', 'qrk' ),
			'promotions'        => __( 'Promotions', 'qrk' ),
			'occupancies'       => __( 'Occupancies', 'qrk' ),
			'departure_post'    => __( 'Departure Post', 'qrk' ),
		];
	}

	/**
	 * Callback for `quark_softrip_sync_departure_updated` action.
	 *
	 * @param mixed[] $data Data passed to the action.
	 *
	 * @return void
	 */
	public function callback_quark_softrip_sync_departure_updated( array $data = [] ): void {
		// Validate data.
		if ( empty( $data ) || empty( $data['post_id'] ) || empty( $data['updated_fields'] ) || empty( $data['softrip_id'] ) ) {
			return;
		}

		// Update fields mapping.
		$fields_mapping = $this->get_departure_update_fields_mapping();

		// Validate what's updated.
		$updated_field_labels = array_filter(
			array_map(
				function ( $field, $is_updated ) use ( $fields_mapping ) {
					return $is_updated ? $fields_mapping[ $field ] : false;
				},
				array_keys( $data['updated_fields'] ),
				array_values( $data['updated_fields'] )
			)
		);

		// Prepare message.
		$message = sprintf(
			// translators: %1$s: Softrip ID, %2$s: Updated Fields.
			__( '"%1$s" synced | Updated fields: %2$s', 'qrk' ),
			strval( $data['softrip_id'] ),
			implode( ', ', $updated_field_labels )
		);

		// Log action.
		$this->log(
			$message,
			[],
			absint( $data['post_id'] ),
			'softrip_sync',
			'sync_departure_updated'
		);
	}

	/**
	 * Callback for `quark_softrip_sync_departure_expired` action.
	 *
	 * @param mixed[] $data Data passed to the action.
	 *
	 * @return void
	 */
	public function callback_quark_softrip_sync_departure_expired( array $data = [] ): void {
		// Validate data.
		if ( empty( $data ) || empty( $data['post_id'] ) || empty( $data['softrip_id'] ) ) {
			return;
		}

		// Prepare message.
		$message = sprintf(
			// translators: %1$s: Softrip ID, %2$d: Post ID.
			__( '"%1$s" expired', 'qrk' ),
			strval( $data['softrip_id'] ),
		);

		// Log action.
		$this->log(
			$message,
			[],
			absint( $data['post_id'] ),
			'softrip_sync',
			'sync_departure_expired'
		);
	}

	/**
	 * Add action links to Stream drop row in admin list screen.
	 *
	 * @param string[] $links  Previous links registered.
	 * @param object   $record Stream record.
	 *
	 * @filter wp_stream_action_links_{connector}
	 *
	 * @return string[]
	 */
	public function action_links( $links, $record ): array { // phpcs:ignore
		// Validate record.
		if ( empty( $record->object_id ) ) {
			return $links;
		}

		// Get post type.
		$post_type = get_post_type( $record->object_id );

		// Validate post type.
		if ( ! $post_type ) {
			return $links;
		}

		// Get post.
		$post = get_post( $record->object_id );

		// Validate post.
		if ( ! $post ) {
			return $links;
		}

		// Validate post type.
		if ( POST_TYPE !== $post_type ) {
			return $links;
		}

		// Edit URL.
		$edit_url = get_edit_post_link( $record->object_id );

		// Validate edit URL.
		if ( empty( $edit_url ) ) {
			return $links;
		}

		// Add edit link.
		$links[ __( 'Edit', 'qrk' ) ] = $edit_url;

		// Return links.
		return $links;
	}
}
