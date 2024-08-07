<?php
/**
 * Softrip Object Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_Post;

/**
 * Object Class.
 */
abstract class Softrip_Object {
	/**
	 * Holds the object data.
	 *
	 * @var array{
	 *     post: WP_Post|null,
	 *     post_meta: mixed[],
	 *     post_taxonomies: mixed[]
	 * } | array{}
	 */
	protected array $data = [];

	/**
	 * Load the data.
	 *
	 * @param int $post_id The object post ID.
	 *
	 * @return void
	 */
	abstract public function load( int $post_id = 0 ): void;

	/**
	 * Get the objects post id.
	 *
	 * @return int
	 */
	public function get_id(): int {
		// If valid post, return ID.
		if ( $this->is_valid() && ! empty( $this->data['post'] ) ) {
			return $this->data['post']->ID;
		}

		// Return a 0 if not.
		return 0;
	}

	/**
	 * Check if the post is valid.
	 *
	 * @return bool
	 */
	public function is_valid(): bool {
		// Check if data is correct.
		if ( empty( $this->data ) ) {
			return false;
		}

		// Return check of validity.
		return $this->data['post'] instanceof WP_Post;
	}

	/**
	 * Get the object data.
	 *
	 * @return array{
	 *     post: WP_Post|null,
	 *     post_meta: mixed[],
	 *     post_taxonomies: mixed[]
	 * } | array{}
	 */
	public function get_data(): array {
		// Return the data array.
		return $this->data;
	}

	/**
	 * Get post meta from the post object.
	 *
	 * @param string $name The metadata to call from the post object.
	 *
	 * @return mixed
	 */
	public function get_post_meta( string $name = '' ): mixed {
		// Check if the post is valid.
		if ( ! $this->is_valid() || empty( $this->data['post_meta'] ) ) {
			// Not valid post, so bail with expected empty meta.
			return '';
		}

		// if not specified, return all.
		if ( empty( $name ) ) {
			return $this->data['post_meta'];
		}

		// Return post meta.
		return $this->data['post_meta'][ $name ] ?? '';
	}

	/**
	 * Get the post status.
	 *
	 * @return string
	 */
	public function get_status(): string {
		// Check if the post is valid.
		if ( $this->is_valid() && ! empty( $this->data['post'] ) ) {
			return $this->data['post']->post_status;
		}

		// Return default status as default.
		return 'draft';
	}
}
