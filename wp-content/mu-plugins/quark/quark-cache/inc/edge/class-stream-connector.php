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
		return 'Edge Cache';
	}

	/**
	 * Return translated context labels.
	 *
	 * @return string[]
	 */
	public function get_context_labels(): array {
		// Return translated context labels.
		return [
			'edge_cache' => 'Edge Cache',
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
			'flushed' => 'Flush & Warm up',
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
		if ( empty( $data ) || empty( $data['time_taken'] ) || ! is_scalar( $data['time_taken'] ) || ! isset( $data['pricing_pages_only'] ) ) {
			return;
		}

		// Format time took to two decimal places.
		$time_taken = number_format( floatval( $data['time_taken'] ), 3 );

		// Pricing pages only flag.
		$pricing_pages_only = (bool) $data['pricing_pages_only'];

		// Prepare message.
		$message = sprintf(
			'Edge cache flushed and warmed in %1$s seconds. Pricing pages only: %2$s',
			$time_taken,
			$pricing_pages_only ? 'Yes' : 'No'
		);

		// Log action.
		$this->log(
			$message,
			[],
			0,
			'edge_cache',
			'flushed'
		);
	}
}
