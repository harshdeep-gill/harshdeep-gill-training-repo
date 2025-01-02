<?php
/**
 * Test suite for the Ships.
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor\Tests\Ships;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Core\get_raw_text_from_html;
use function Quark\Ingestor\get_image_details;
use function Quark\Ingestor\get_post_modified_time;
use function Quark\Ingestor\Ships\get_ship_amenities_data;
use function Quark\Ingestor\Ships\get_ship_data;
use function Quark\Ingestor\Ships\get_ship_specifications_data;
use function Quark\Ships\bust_post_cache;

use const Quark\Ingestor\Ships\SHIP_SPECIFICATION_MAPPING;
use const Quark\Ships\POST_TYPE;
use const Quark\Tests\Ingestor\TEST_IMAGE_PATH;

/**
 * Class Test_Ships
 */
class Test_Ships extends Softrip_TestCase {
	/**
	 * Test get ship amenities data.
	 *
	 * @covers \Quark\Ingestor\Ships\get_ship_amenities_data
	 *
	 * @return void
	 */
	public function test_get_ship_amenities_data(): void {
		// Default data.
		$default_expected_data = [
			'cabin'      => [],
			'aboard'     => [],
			'activities' => [],
		];

		// Test with empty arg.
		$this->assertEquals( $default_expected_data, get_ship_amenities_data() );

		// Test with default arg.
		$this->assertEquals( $default_expected_data, get_ship_amenities_data( 0 ) );

		// Test with non-existing ship.
		$this->assertEquals( $default_expected_data, get_ship_amenities_data( 999999 ) );

		// Create a ship post.
		$ship_post_id = $this->factory()->post->create( [ 'post_type' => POST_TYPE ] );
		$this->assertIsInt( $ship_post_id );
		$this->assertNotEmpty( $ship_post_id );

		// Test without any meta.
		$this->assertEquals( $default_expected_data, get_ship_amenities_data( $ship_post_id ) );

		// Create another ship post.
		$ship_post_id_2 = $this->factory()->post->create(
			[
				'post_type'  => POST_TYPE,
				'meta_input' => [
					'cabin_0_item'      => 'Carpeting',
					'cabin_1_item'      => 'Bathrooms have heated floors',
					'cabin_2_item'      => 'Fridges',
					'aboard_0_item'     => 'Dining room',
					'aboard_1_item'     => 'Lounge',
					'aboard_2_item'     => 'Library',
					'activities_0_item' => 'Activities1',
					'activities_1_item' => 'Activities2',
					'activities_2_item' => 'Activities3',
				],
			]
		);
		$this->assertIsInt( $ship_post_id_2 );
		$this->assertNotEmpty( $ship_post_id_2 );

		// Test with meta.
		$expected_data = [
			'cabin'      => [
				'Carpeting',
				'Bathrooms have heated floors',
				'Fridges',
			],
			'aboard'     => [
				'Dining room',
				'Lounge',
				'Library',
			],
			'activities' => [
				'Activities1',
				'Activities2',
				'Activities3',
			],
		];
		$this->assertEquals( $expected_data, get_ship_amenities_data( $ship_post_id_2 ) );
	}

	/**
	 * Test get ship specifications data.
	 *
	 * @covers \Quark\Ingestor\Ships\get_ship_specifications_data
	 *
	 * @return void
	 */
	public function test_get_ship_specifications_data(): void {
		// Default expected data.
		$default_expected_data = [];

		// Test with empty arg.
		$this->assertEquals( $default_expected_data, get_ship_specifications_data() );

		// Test with default arg.
		$this->assertEquals( $default_expected_data, get_ship_specifications_data( 0 ) );

		// Test with non-existing ship.
		$this->assertEquals( $default_expected_data, get_ship_specifications_data( 999999 ) );

		// Create a ship post.
		$ship_post_id = $this->factory()->post->create( [ 'post_type' => POST_TYPE ] );
		$this->assertIsInt( $ship_post_id );

		// Test without any meta.
		$this->assertEquals( $default_expected_data, get_ship_specifications_data( $ship_post_id ) );

		// Initialize ship specifications data.
		$expected_data = [];

		// Add meta data to the ship post.
		foreach ( SHIP_SPECIFICATION_MAPPING as $ship_specification ) {
			$meta_key = $ship_specification['meta_key'];

			// Generate random meta value.
			$meta_value = $meta_key . '_' . wp_rand( 0, 100 );

			// Update the ship post with the meta value.
			update_post_meta( $ship_post_id, $meta_key, $meta_value );

			// Add the meta value to the expected data.
			$expected_data[ $ship_specification['payload_key'] ] = [
				'id'    => $ship_specification['id'],
				'type'  => $ship_specification['type'],
				'value' => $meta_value,
			];
		}

		// Bust ship post cache.
		bust_post_cache( $ship_post_id );

		// Test with meta.
		$this->assertEquals( $expected_data, get_ship_specifications_data( $ship_post_id ) );
	}

	/**
	 * Test get ship data.
	 *
	 * @covers \Quark\Ingestor\Ships\get_ship_data
	 *
	 * @return void
	 */
	public function test_get_ship_data(): void {
		// Default expected data.
		$default_expected_data = [];

		// Test with empty arg.
		$this->assertEquals( $default_expected_data, get_ship_data() );

		// Test with default arg.
		$this->assertEquals( $default_expected_data, get_ship_data( 0 ) );

		// Test with non-existing ship.
		$this->assertEquals( $default_expected_data, get_ship_data( 999999 ) );

		// Create a ship post.
		$ship_post_id = $this->factory()->post->create(
			[
				'post_type'  => POST_TYPE,
				'meta_input' => [
					'ship_code'   => 'OQP',
					'drupal_id'   => 834,
					'description' => 'Lorem ipsum dolor sit amet',
				],
			]
		);
		$this->assertIsInt( $ship_post_id );

		// Test.
		$expected_data = [
			'id'             => $ship_post_id,
			'code'           => 'OQP',
			'name'           => get_raw_text_from_html( get_the_title( $ship_post_id ) ),
			'url'            => get_permalink( $ship_post_id ),
			'description'    => 'Lorem ipsum dolor sit amet',
			'modified'       => get_post_modified_time( $ship_post_id ),
			'specifications' => get_ship_specifications_data( $ship_post_id ),
			'amenities'      => get_ship_amenities_data( $ship_post_id ),
			'deckPlanImage'  => [],
			'heroImage'      => [],
			'images'         => [],
		];
		$actual_data   = get_ship_data( $ship_post_id );
		$this->assertEquals( $expected_data, $actual_data );

		// Create attachments.
		$media_post_id1 = $this->factory()->attachment->create_upload_object( TEST_IMAGE_PATH );
		$this->assertIsInt( $media_post_id1 );
		$media_post_id2 = $this->factory()->attachment->create_upload_object( TEST_IMAGE_PATH );
		$this->assertIsInt( $media_post_id2 );

		// Add deck plan image.
		update_post_meta( $ship_post_id, 'deck_plan_image', $media_post_id1 );

		// Bust ship post cache.
		bust_post_cache( $ship_post_id );

		// Update expected data.
		$expected_data['deckPlanImage'] = get_image_details( $media_post_id1 );

		// Test with deck plan image.
		$actual_data = get_ship_data( $ship_post_id );
		$this->assertEquals( $expected_data, $actual_data );

		// Add collage images.
		$post_content = sprintf(
			'
            <!-- wp:quark/collage -->
                <!-- wp:quark/collage-media-item {"image":{"id":%1$s,"src":"%2$s","width":1458,"height":875,"alt":"","caption":"","title":"e37f6ddb-dd1c-4148-a821-95d06a8dd4fc","size":"full"}} /-->

                <!-- wp:quark/collage-media-item {"image":{"id":%3$s,"src":"%4$s","width":1315,"height":875,"alt":"","caption":"","title":"b898271c-219e-4203-bd40-20be0a749fc7","size":"full"}} /-->
            <!-- /wp:quark/collage -->
            ',
			$media_post_id1,
			wp_get_attachment_image_url( $media_post_id1, 'medium' ),
			$media_post_id2,
			wp_get_attachment_image_url( $media_post_id2, 'full' )
		);

		// Update the ship post content.
		wp_update_post(
			[
				'ID'           => $ship_post_id,
				'post_content' => $post_content,
			]
		);

		// Test.
		$expected_data['images'] = [
			get_image_details( $media_post_id1 ),
			get_image_details( $media_post_id2 ),
		];
		$actual_data             = get_ship_data( $ship_post_id );
		$this->assertEquals( $expected_data, $actual_data );

		// Set featured image.
		set_post_thumbnail( $ship_post_id, $media_post_id1 );

		// Bust ship post cache.
		bust_post_cache( $ship_post_id );

		// Update expected data.
		$expected_data['heroImage'] = get_image_details( $media_post_id1 );

		// Test with featured image.
		$actual_data = get_ship_data( $ship_post_id );
		$this->assertEquals( $expected_data, $actual_data );
	}
}
