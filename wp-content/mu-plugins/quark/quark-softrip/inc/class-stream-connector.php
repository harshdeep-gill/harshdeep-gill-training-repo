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
		'quark_softrip_sync_error',
		'quark_softrip_sync_departure_no_updates',
		'quark_softrip_sync_departure_drafted',
	];

	/**
	 * Return translated connector label.
	 *
	 * @return string
	 */
	public function get_label(): string {
		// Return label.
		return 'Softrip';
	}

	/**
	 * Return translated context labels.
	 *
	 * @return string[]
	 */
	public function get_context_labels(): array {
		// Return labels.
		return [
			'softrip_sync' => 'Softrip Sync',
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
			'sync_initiated'            => 'Sync Initiated',
			'sync_completed'            => 'Sync Completed',
			'sync_departure_updated'    => 'Departure Updated',
			'sync_departure_expired'    => 'Departure Expired',
			'sync_error'                => 'Sync Error',
			'sync_departure_no_updates' => 'Departure No Updates',
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
			'Softrip sync initiated via %1$s | Total %2$s : %3$d',
			strval( $data['via'] ),
			_n( 'itinerary', 'itineraries', absint( $data['count'] ), 'qrk' ),
			absint( $data['count'] )
		);

		// Log action.
		$this->log(
			$message,
			[],
			0,
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
			'Softrip sync completed via %1$s | Successful %4$s: %2$d | Failed %5$s: %3$d',
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
			0,
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
			'adventure_options' => 'Adventure Options',
			'promotions'        => 'Promotions',
			'occupancies'       => 'Occupancies',
			'departure_post'    => 'Departure Post',
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
			'"%1$s" synced | Updated fields: %2$s',
			$data['softrip_id'],
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
	 * Callback for `quark_softrip_sync_departure_drafted` action.
	 *
	 * @param mixed[] $data Data passed to the action.
	 *
	 * @return void
	 */
	public function callback_quark_softrip_sync_departure_drafted( array $data = [] ): void {
		// Validate data.
		if ( empty( $data ) || empty( $data['post_id'] ) || empty( $data['softrip_package_id'] ) ) {
			return;
		}

		// Prepare message.
		$message = sprintf(
			// translators: %1$s: Softrip ID.
			__( '"%1$s" drafted', 'qrk' ),
			strval( $data['softrip_package_id'] )
		);

		// Log action.
		$this->log(
			$message,
			[],
			absint( $data['post_id'] ),
			'softrip_sync',
			'sync_departure_drafted'
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
			'"%1$s" expired',
			$data['softrip_id'],
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
	 * Callback for `quark_softrip_sync_error` action.
	 *
	 * @param mixed[] $data Data passed to the action.
	 *
	 * @return void
	 */
	public function callback_quark_softrip_sync_error( array $data = [] ): void {
		// Validate data.
		if ( empty( $data ) || empty( $data['error'] ) || empty( $data['via'] ) ) {
			return;
		}

		// Check if codes are available.
		if ( ! empty( $data['codes'] ) && is_array( $data['codes'] ) ) {
			// Prepare message.
			$message = sprintf(
				'Softrip sync error via %1$s | Error: %2$s | Codes: %3$s',
				$data['via'],
				strval( $data['error'] ),
				implode( ', ', $data['codes'] )
			);
		} else {
			// Prepare message.
			$message = sprintf(
				'Softrip sync error via %1$s | Error: %2$s',
				$data['via'],
				strval( $data['error'] )
			);
		}

		// Log action.
		$this->log(
			$message,
			[],
			0,
			'softrip_sync',
			'sync_error'
		);
	}

	/**
	 * Callback for `quark_softrip_sync_departure_no_updates` action.
	 *
	 * @param mixed[] $data Data passed to the action.
	 *
	 * @return void
	 */
	public function callback_quark_softrip_sync_departure_no_updates( array $data = [] ): void {
		// Validate data.
		if ( empty( $data ) || empty( $data['post_id'] ) || empty( $data['softrip_id'] ) ) {
			return;
		}

		// Prepare message.
		$message = sprintf(
			'"%1$s" no updates',
			$data['softrip_id']
		);

		// Log action.
		$this->log(
			$message,
			[],
			absint( $data['post_id'] ),
			'softrip_sync',
			'sync_departure_no_updates'
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
		$links['Edit'] = $edit_url;

		// Return links.
		return $links;
	}
}
