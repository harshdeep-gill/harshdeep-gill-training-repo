<?php
/**
 * Stream connector for cleanup.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Cleanup;

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
	public $name = 'quark_softrip_cleanup';

	/**
	 * Actions registered for this connector.
	 *
	 * @var string[]
	 */
	public $actions = [
		'quark_softrip_cleanup_initiated',
		'quark_softrip_cleanup_completed',
		'quark_softrip_cleanup_failed',
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
			'softrip_cleanup' => 'Softrip Cleanup',
		];
	}

	/**
	 * Return translated action labels.
	 *
	 * @return string[]
	 */
	public function get_action_labels(): array {
		// Return labels.
		return [
			'cleanup_initiated' => 'Cleanup Initiated',
			'cleanup_completed' => 'Cleanup Completed',
			'cleanup_failed'    => 'Cleanup Failed',
		];
	}

	/**
	 * Callback for `quark_softrip_cleanup_initiated` action.
	 *
	 * @param mixed[] $data Data passed to the action.
	 *
	 * @return void
	 */
	public function callback_quark_softrip_cleanup_initiated( array $data = [] ): void {
		// Validate data.
		if ( empty( $data ) || empty( $data['via'] ) || ! isset( $data['total'] ) ) {
			return;
		}

		// Prepare message.
		$message = sprintf(
			// translators: %1$s: Via, %2$d: Total, %3$s: Departures.
			'Softrip cleanup initiated by %1$s for %2$d %3$s.',
			strval( $data['via'] ),
			absint( $data['total'] ),
			_n( 'departure', 'departures', absint( $data['total'] ), 'qrk' )
		);

		// Log the action.
		$this->log(
			$message,
			$data,
			absint( wp_unique_id() ),
			'softrip_cleanup',
			'cleanup_initiated'
		);
	}

	/**
	 * Callback for `quark_softrip_cleanup_completed` action.
	 *
	 * @param mixed[] $data Data passed to the action.
	 *
	 * @return void
	 */
	public function callback_quark_softrip_cleanup_completed( array $data = [] ): void {
		// Validate data.
		if ( empty( $data ) || empty( $data['via'] ) || ! isset( $data['success'] ) || ! isset( $data['total'] ) ) {
			return;
		}

		// Prepare message.
		$message = sprintf(
			// translators: %1$s: Via, %2$d: Successful, %3$s: Departures, %4$d: Total.
			'Softrip cleanup completed by %1$s with %2$d successful %3$s out of %4$d.',
			strval( $data['via'] ),
			absint( $data['success'] ),
			_n( 'departure', 'departures', absint( $data['success'] ), 'qrk' ),
			absint( $data['total'] )
		);

		// Log the action.
		$this->log(
			$message,
			$data,
			absint( wp_unique_id() ),
			'softrip_cleanup',
			'cleanup_completed'
		);
	}

	/**
	 * Callback for `quark_softrip_cleanup_failed` action.
	 *
	 * @param mixed[] $data Data passed to the action.
	 *
	 * @return void
	 */
	public function callback_quark_softrip_cleanup_failed( array $data = [] ): void {
		// Validate data.
		if ( empty( $data ) || empty( $data['via'] ) || empty( $data['departure_post_id'] ) || empty( $data['message'] ) ) {
			return;
		}

		// Prepare message.
		$message = sprintf(
			// translators: %1$s: Via, %2$d: Departure ID, %3$s: Message.
			'Softrip cleanup failed by %1$s for departure %2$d with message: %3$s.',
			strval( $data['via'] ),
			absint( $data['departure_post_id'] ),
			strval( $data['message'] )
		);

		// Log the action.
		$this->log(
			$message,
			$data,
			absint( wp_unique_id() ),
			'softrip_cleanup',
			'cleanup_failed'
		);
	}
}
