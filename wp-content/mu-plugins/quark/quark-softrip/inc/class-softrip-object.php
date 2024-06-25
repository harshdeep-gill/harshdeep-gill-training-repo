<?php
/**
 * Softtrip Object Class.
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
	 * }
	 */
	protected array $data;

	/**
	 * Load the data.
	 *
	 * @param int $post_id The object post ID.
	 *
	 * @return void
	 */
	abstract public function load( int $post_id = 0 ): void;
	// phpcs:ignore Travelopia.Functions.CommentOnFirstLineOfFunctions.Missing

	/**
	 * Get the objects post id.
	 *
	 * @return int
	 */
	public function get_id(): int {
		// If valid post, return ID.
		if ( $this->is_valid() ) {
			return $this->data['post'] ? $this->data['post']->ID : 0;
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
	 * Get post meta from the post object.
	 *
	 * @param string $name The metadata to call from the post object.
	 *
	 * @return mixed
	 */
	public function post_meta( string $name = '' ): mixed {
		// Check if the post is valid.
		if ( ! $this->is_valid() ) {
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
}
