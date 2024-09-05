<?php
/**
 * Stream Connector.
 *
 * @package quark-search
 */

namespace Quark\Search;

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
	public $name = 'quark_search';

	/**
	 * Actions registered for this connector.
	 *
	 * @var string[]
	 */
	public $actions = [
		'quark_search_reindex_initiated',
		'quark_search_reindex_completed',
		'quark_search_reindex_failed',
	];

	/**
	 * Return translated connector label.
	 *
	 * @return string
	 */
	public function get_label(): string {
		// Return label.
		return __( 'Search', 'qrk' );
	}

	/**
	 * Return translated context labels.
	 *
	 * @return string[]
	 */
	public function get_context_labels(): array {
		// Return labels.
		return [
			'search_reindex' => __( 'Solr search reindex', 'qrk' ),
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
			'reindex_initiated' => __( 'Solr reindex Initiated', 'qrk' ),
			'reindex_completed' => __( 'Solr reindex Completed', 'qrk' ),
			'reindex_failed'    => __( 'Solr reindex Failed', 'qrk' ),
		];
	}

	/**
	 * Callback for `quark_search_reindex_initiated` action.
	 *
	 * @param mixed[] $args Action arguments.
	 *
	 * @return void
	 */
	public function callback_quark_search_reindex_initiated( array $args = [] ): void {
		// Validate data.
		if ( empty( $args ) || ! isset( $args['total'] ) ) {
			return;
		}

		// Prepare message.
		$message = sprintf(
			/* translators: %s: Total number of items to reindex. */
			__( 'Solr reindex initiated. Total items to reindex: %1$d.', 'qrk' ),
			absint( $args['total'] )
		);

		// Log message.
		$this->log(
			$message,
			$args,
			absint( wp_unique_id() ),
			'search_reindex',
			'reindex_initiated'
		);
	}

	/**
	 * Callback for `quark_search_reindex_completed` action.
	 *
	 * @param mixed[] $args Action arguments.
	 *
	 * @return void
	 */
	public function callback_quark_search_reindex_completed( array $args = [] ): void {
		// Validate data.
		if ( empty( $args ) || ! isset( $args['success'] ) || ! isset( $args['failed'] ) || ! isset( $args['total'] ) ) {
			return;
		}

		// Prepare message.
		$message = sprintf(
			// translators: %1$d: Total, %2$d: Success, %3$d: Failed.
			__( 'Solr reindex completed. Total: %1$d, Success: %2$d, Failed: %3$d .', 'qrk' ),
			absint( $args['total'] ),
			absint( $args['success'] ),
			absint( $args['failed'] )
		);

		// Log message.
		$this->log(
			$message,
			$args,
			absint( wp_unique_id() ),
			'search_reindex',
			'reindex_completed'
		);
	}

	/**
	 * Callback for `quark_search_reindex_failed` action.
	 *
	 * @param mixed[] $args Action arguments.
	 *
	 * @return void
	 */
	public function callback_quark_search_reindex_failed( array $args = [] ): void {
		// Validate data.
		if ( empty( $args ) || ! isset( $args['error'] ) || empty( $args['post_id'] ) ) {
			return;
		}

		// Prepare message.
		$message = sprintf(
			/* translators: %s: Error message. */
			__( 'Solr reindex failed for post ID %1$d with error %2$s.', 'qrk' ),
			absint( $args['post_id'] ),
			strval( $args['error'] )
		);

		// Log message.
		$this->log(
			$message,
			$args,
			absint( wp_unique_id() ),
			'search_reindex',
			'reindex_failed'
		);
	}
}
