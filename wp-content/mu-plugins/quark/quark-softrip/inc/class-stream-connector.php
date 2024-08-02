<?php
/**
 * Stream Connector.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_Stream\Connector;

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
			'initiated' => __( 'Sync Initiated', 'qrk' ),
			'completed' => __( 'Sync Completed', 'qrk' ),
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
			'initiated'
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
			// translators: %1$s: Via, %2$d: Successful, %3$d: Failed.
			__( 'Softrip sync completed via %1$s | Successful: %2$d | Failed: %3$d', 'qrk' ),
			strval( $data['via'] ),
			absint( $data['success'] ),
			absint( $data['failed'] )
		);

		// Log action.
		$this->log(
			$message,
			[],
			absint( wp_unique_id() ),
			'softrip_sync',
			'completed'
		);
	}
}
