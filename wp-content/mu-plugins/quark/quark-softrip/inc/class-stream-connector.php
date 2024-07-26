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
		'softrip_sync_initiated',
		'softrip_sync_completed',
	];

	/**
	 * Return translated connector label.
	 *
	 * @return string
	 */
	public function get_label(): string {
		// Return label.
		return __( 'Softrip Sync', 'qrk' );
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

	public function callback_softrip_sync_initiated( $record_id, $context ) {
		// Set action.
		$action = __( 'softrip_sync_initiated', 'qrk' );

		$this->log(
		// Message to store in log
			sprintf( __( 'Softrip sync %s', 'qrk' ), $context['message'] ),
			// Context for the log entry
			$context,
			// ID related to the action
			$record_id,
			// Context label
			'softrip_sync',
			// Action label
			$context['action']
		);
	}
}
