<?php
/**
 * Stream connector.
 *
 * @package quark-cache
 */

namespace Quark\Cache\Edge;

use WP_Stream\Connector;

/**
 * Class Stream_Connector.
 */
class Stream_Connector extends Connector {
	/**
	 * Connector slug.
	 *
	 * @var string
	 */
	public $name = 'quark_edge_cache';

	/**
	 * Actions registered for this connector.
	 *
	 * @var string[]
	 */
	public $actions = [
		'quark_edge_cache_flushed',
	];

	/**
	 * Return translated connector label.
	 *
	 * @return string
	 */
	public function get_label(): string {
		// Return translated connector label.
		return __( 'Page Cache', 'qrk' );
	}

	/**
	 * Return translated context labels.
	 *
	 * @return string[]
	 */
	public function get_context_labels(): array {
		// Return translated context labels.
		return [
			'page_cache' => __( 'Page Cache', 'qrk' ),
		];
	}

	/**
	 * Return translated action labels.
	 *
	 * @return string[]
	 */
	public function get_action_labels(): array {
		// Return translated action labels.
		return [
			'flushed' => __( 'Flush & Warm up', 'qrk' ),
		];
	}

	/**
	 * Callback for `quark_edge_cache_flushed` action.
	 *
	 * @param mixed[] $data Data passed to the action.
	 *
	 * @return void
	 */
	public function callback_quark_edge_cache_flushed( array $data = [] ): void {
		// Validate data.
		if ( empty( $data ) || empty( $data['time_taken'] ) || ! is_scalar( $data['time_taken'] ) ) {
			return;
		}

		// Format time took to two decimal places.
		$time_taken = number_format( floatval( $data['time_taken'] ), 3 );

		// Prepare message.
		$message = sprintf(
			// translators: %s is the time took in seconds.
			__( 'Edge cache flushed and warmed up. Time taken: %s seconds.', 'qrk' ),
			$time_taken
		);

		// Log action.
		$this->log(
			$message,
			[],
			0,
			'page_cache',
			'flushed'
		);
	}
}
