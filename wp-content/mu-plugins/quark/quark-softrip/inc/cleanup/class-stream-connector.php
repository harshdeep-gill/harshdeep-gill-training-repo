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
			'softrip_cleanup' => __( 'Softrip Cleanup', 'qrk' ),
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
			'cleanup_initiated' => __( 'Cleanup Initiated', 'qrk' ),
			'cleanup_completed' => __( 'Cleanup Completed', 'qrk' ),
			'cleanup_failed'    => __( 'Cleanup Failed', 'qrk' ),
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
		// Log the action.
		$this->log(
			'Cleanup initiated.',
			$data,
			0,
			'quark_softrip_cleanup_initiated',
			'softrip_cleanup'
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
		// Log the action.
		$this->log(
			'Cleanup completed.',
			$data,
			0,
			'quark_softrip_cleanup_completed',
			'softrip_cleanup'
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
		// Log the action.
		$this->log(
			'Cleanup failed.',
			$data,
			0,
			'quark_softrip_cleanup_failed',
			'softrip_cleanup'
		);
	}
}
