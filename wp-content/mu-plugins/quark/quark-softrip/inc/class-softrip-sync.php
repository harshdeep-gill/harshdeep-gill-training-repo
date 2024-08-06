<?php
/**
 * Softrip Sync Class.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_Query;
use WP_Error;

use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;

/**
 * Class Softrip_Sync.
 */
class Softrip_Sync {

	/**
	 * List of prepared itinerary ids with Softrip ids.
	 *
	 * @var array<string, int>
	 */
	protected array $prepared_codes = [];

	/**
	 * Sync a softrip code.
	 *
	 * @param string  $softrip_code The softrip code to sync.
	 * @param mixed[] $data         The data to sync to the code.
	 *
	 * @return bool
	 */
	public function sync_softrip_code( string $softrip_code = '', array $data = [] ): bool {
		// Check ID has been prepared.
		if ( empty( $this->prepared_codes[ $softrip_code ] ) ) {
			return false;
		}

		// Get itinerary and update data.
		$post_id   = $this->prepared_codes[ $softrip_code ];
		$itinerary = new Itinerary( $post_id );
		$itinerary->update_departures( $data );

		// Return true to indicate done.
		return true;
	}

	/**
	 * Call a batch of 5 Softrip Codes.
	 *
	 * @param array<int, int|string> $codes Softrip codes array.
	 *
	 * @return mixed[]
	 */
	public function batch_request( array $codes = [] ): array {
		// Get the raw departure data for codes.
		$raw_departures = synchronize_itinerary_departures( $codes );

		// Handle if an error is found.
		if ( ! is_array( $raw_departures ) ) {
			// Fire action and pass failed ID's for logging.
			do_action( 'softrip_sync_failed', $raw_departures );

			// Return empty array.
			return [];
		}

		// Return valid response.
		return $raw_departures;
	}

	/**
	 * Split a list of itinerary ID's into batches of 5.
	 *
	 * @param int[] $ids        Array of ID's to split.
	 * @param int   $batch_size The size of the batch.
	 *
	 * @return array<int, array<int, int|string>>
	 */
	public function prepare_batch_ids( array $ids = [], int $batch_size = 5 ): array {
		// Ensure batch size is at least 1.
		$batch_size = max( 1, $batch_size );

		// Create packages.
		foreach ( $ids as $id ) {
			$softrip_package_id = strval( get_post_meta( $id, 'softrip_package_id', true ) );

			// Capture if a package id is found.
			if ( ! empty( $softrip_package_id ) ) {
				$this->prepared_codes[ $softrip_package_id ] = $id;
			}
		}

		// Chunk to sync into packages.
		return array_chunk( array_keys( $this->prepared_codes ), $batch_size );
	}

	/**
	 * Get ID's of itinerary posts to sync.
	 *
	 * @return int[]
	 */
	public function get_all_itinerary_ids(): array {
		// Args to get all items at once.
		$args = [
			'post_type'              => ITINERARY_POST_TYPE,
			'posts_per_page'         => -1, // -1 means retrieve all posts.
			'fields'                 => 'ids', // Retrieve only IDs.
			'post_status'            => 'draft,publish', // Only published itineraries.
			'no_found_rows'          => true, // Improve query performance.
			'update_post_meta_cache' => false, // Disable post meta cache for performance.
			'update_post_term_cache' => false, // Disable post term cache for performance.
			'ignore_sticky_posts'    => true, // Ignore sticky posts.
		];

		// Run a single query to get all itinerary IDs.
		$query = new WP_Query( $args );

		// Return the array of IDs.
		return array_map( 'absint', $query->posts );
	}
}
