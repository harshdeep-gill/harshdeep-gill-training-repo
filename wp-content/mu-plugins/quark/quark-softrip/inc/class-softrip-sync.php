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
		$itinerary->update_departures( (array) $data );

		// Return true to indicate done.
		return true;
	}

	/**
	 * Get a list of prepared codes.
	 *
	 * @return array<string, int>
	 */
	public function get_prepared_codes(): array {
		// Return the list of prepared codes.
		return $this->prepared_codes;
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
		$raw_departures = request_departures( $codes );

		// Handle if an error is found.
		if ( ! is_array( $raw_departures ) ) {
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
		// Start sync items.
		$batches = [];

		// Ensure batch size is at least 1.
		$batch_size = max( 1, $batch_size );

		// Create packages.
		foreach ( $ids as $id ) {
			$softrip_package_id = strval( get_post_meta( $id, 'softrip_package_id', true ) );

			// Capture if a package id is found.
			if ( ! empty( $softrip_package_id ) ) {
				$this->prepared_codes[ $softrip_package_id ] = $id;
				$batches[]                                   = $id;
			}
		}

		// Chunk to sync into packages.
		return array_chunk( array_keys( $batches ), $batch_size );
	}

	/**
	 * Get ID's of itineraries to sync.
	 *
	 * @return int[]
	 */
	public function get_itinerary_ids(): array {
		// Args to get items.
		$args = [
			'post_type'              => ITINERARY_POST_TYPE,
			'posts_per_page'         => 100,
			'fields'                 => 'ids',
			'offset'                 => 0,
			'post_status'            => 'publish', // Lets not get Departures for drafts Itinerary.
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'ignore_sticky_posts'    => true,
		];

		// Run the query.
		$query = new WP_Query( $args );

		// Set the count.
		$found_posts = $query->found_posts;
		$processed   = 0;
		$ids         = [];

		// Get the post ids.
		while ( $processed < $found_posts ) {
			// Loop over the posts.
			foreach ( $query->posts as $post ) {
				$ids[] = absint( $post );
				++$processed;
			}
			$args['offset'] = $processed;
			$query          = new WP_Query( $args );
		}

		// Return the ID array.
		return $ids;
	}
}
