<?php
/**
 * Stream Connector.
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor;

use WP_Stream\Connector;

use const Quark\Expeditions\POST_TYPE;

/**
 * Class Stream Connector.
 */
class Stream_Connector extends Connector {

	/**
	 * Connector slug.
	 *
	 * @var string
	 */
	public $name = 'quark_ingestor_push';

	/**
	 * Actions registered for this connector.
	 *
	 * @var string[]
	 */
	public $actions = [
		'quark_ingestor_push_initiated',
		'quark_ingestor_push_completed',
		'quark_ingestor_push_error',
		'quark_ingestor_push_success',
	];

	/**
	 * Return translated connector label.
	 *
	 * @return string
	 */
	public function get_label(): string {
		// Return label.
		return __( 'Ingestor', 'qrk' );
	}

	/**
	 * Return translated context labels.
	 *
	 * @return string[]
	 */
	public function get_context_labels(): array {
		// Return labels.
		return [
			'ingestor_push' => __( 'Ingestor Push', 'qrk' ),
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
			'push_initiated' => __( 'Push Initiated', 'qrk' ),
			'push_completed' => __( 'Push Completed', 'qrk' ),
			'push_error'     => __( 'Push Error', 'qrk' ),
			'push_success'   => __( 'Push Success', 'qrk' ),
		];
	}

	/**
	 * Callback for `quark_ingestor_push_initiated` action.
	 *
	 * @param mixed[] $data Data passed to the action.
	 *
	 * @return void
	 */
	public function callback_quark_ingestor_push_initiated( array $data = [] ): void {
		// Validate data.
		if ( empty( $data ) || empty( $data['expedition_post_ids'] ) || empty( $data['initiated_via'] ) || empty( $data['total_count'] ) || ! isset( $data['changed_only'] ) ) {
			return;
		}

		// Get expedition post IDs.
		$expedition_post_ids = $data['expedition_post_ids'];

		// Get initiated via.
		$initiated_via = strval( $data['initiated_via'] );

		// Get changed only.
		$changed_only = (bool) $data['changed_only'];

		// Get total count.
		$total_count = absint( $data['total_count'] );

		// Prepare message.
		$message = sprintf(
			/* translators: 1: Total count, 2: Initiated via, 3: Changed only */
			__( 'Ingestor push initiated for %1$d expedition(s) via %2$s | Changed only: %3$s.', 'qrk' ),
			$total_count,
			$initiated_via,
			$changed_only ? __( 'Yes', 'qrk' ) : __( 'No', 'qrk' )
		);

		// Log message.
		$this->log(
			$message,
			[
				'expedition_post_ids' => $expedition_post_ids,
				'initiated_via'       => $initiated_via,
				'changed_only'        => $changed_only,
				'total_count'         => $total_count,
			],
			0,
			'ingestor_push',
			'push_initiated'
		);
	}

	/**
	 * Callback for `quark_ingestor_push_completed` action.
	 *
	 * @param mixed[] $data Data passed to the action.
	 *
	 * @return void
	 */
	public function callback_quark_ingestor_push_completed( array $data = [] ): void {
		// Validate data.
		if ( empty( $data ) || empty( $data['expedition_post_ids'] ) || empty( $data['initiated_via'] ) || ! isset( $data['success_count'] ) || ! isset( $data['total_count'] ) || ! isset( $data['changed_only'] ) ) {
			return;
		}

		// Get expedition post IDs.
		$expedition_post_ids = $data['expedition_post_ids'];

		// Get initiated via.
		$initiated_via = strval( $data['initiated_via'] );

		// Get success count.
		$success_count = absint( $data['success_count'] );

		// Get total count.
		$total_count = absint( $data['total_count'] );

		// Get changed only.
		$changed_only = (bool) $data['changed_only'];

		// Prepare message.
		$message = sprintf(
			/* translators: 1: Total count, 2: Initiated via, 3: Success count, 4: Changed only */
			__( 'Ingestor push completed for %1$d expedition(s) via %2$s | Successful: %3$d | Changed only: %4$s.', 'qrk' ),
			$total_count,
			$initiated_via,
			$success_count,
			$changed_only ? __( 'Yes', 'qrk' ) : __( 'No', 'qrk' )
		);

		// Log message.
		$this->log(
			$message,
			[
				'expedition_post_ids' => $expedition_post_ids,
				'initiated_via'       => $initiated_via,
				'success_count'       => $success_count,
				'total_count'         => $total_count,
				'changed_only'        => $changed_only,
			],
			0,
			'ingestor_push',
			'push_completed'
		);
	}

	/**
	 * Callback for `quark_ingestor_push_error` action.
	 *
	 * @param mixed[] $data Data passed to the action.
	 *
	 * @return void
	 */
	public function callback_quark_ingestor_push_error( array $data = [] ): void {
		// Validate data.
		if ( empty( $data ) || empty( $data['error'] ) || empty( $data['initiated_via'] ) ) {
			return;
		}

		// Get expedition post ID.
		$expedition_post_id = absint( $data['expedition_post_id'] ?? 0 );

		// Get initiated via.
		$initiated_via = strval( $data['initiated_via'] );

		// Get error.
		$error = strval( $data['error'] );

		// Initialize message.
		$message = '';

		// Prepare message.
		if ( empty( $expedition_post_id ) ) {
			$message = sprintf(
				/* translators: 1: Initiated via, 2: Error message */
				__( 'Ingestor push failed via %1$s | %2$s', 'qrk' ),
				$initiated_via,
				$error
			);
		} else {
			$message = sprintf(
				/* translators: 1: Expedition post title, 2: Initiated via, 3: Error message */
				__( 'Ingestor push failed for expedition "%1$s" via %2$s | %3$s', 'qrk' ),
				get_the_title( $expedition_post_id ),
				$initiated_via,
				$error
			);
		}

		// Log message.
		$this->log(
			$message,
			[
				'expedition_post_id' => $expedition_post_id,
				'initiated_via'      => $initiated_via,
				'error'              => $error,
			],
			$expedition_post_id,
			'ingestor_push',
			'push_error'
		);
	}

	/**
	 * Callback for `quark_ingestor_push_success` action.
	 *
	 * @param mixed[] $data Data passed to the action.
	 *
	 * @return void
	 */
	public function callback_quark_ingestor_push_success( array $data = [] ): void {
		// Validate data.
		if ( empty( $data ) || empty( $data['expedition_post_id'] ) || empty( $data['initiated_via'] ) || ! isset( $data['changed_only'] ) ) {
			return;
		}

		// Get expedition post ID.
		$expedition_post_id = absint( $data['expedition_post_id'] );

		// Get initiated via.
		$initiated_via = strval( $data['initiated_via'] );

		// Get changed only.
		$changed_only = (bool) $data['changed_only'];

		// Prepare message.
		$message = sprintf(
			/* translators: 1: Expedition post title, 2: Initiated via, 3: Changed only */
			__( 'Ingestor push successful for expedition "%1$s" via %2$s | Changed only: %3$s.', 'qrk' ),
			get_the_title( $expedition_post_id ),
			$initiated_via,
			$changed_only ? __( 'Yes', 'qrk' ) : __( 'No', 'qrk' )
		);

		// Log message.
		$this->log(
			$message,
			[
				'expedition_post_id' => $expedition_post_id,
				'initiated_via'      => $initiated_via,
				'changed_only'       => $changed_only,
			],
			$expedition_post_id,
			'ingestor_push',
			'push_success'
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
