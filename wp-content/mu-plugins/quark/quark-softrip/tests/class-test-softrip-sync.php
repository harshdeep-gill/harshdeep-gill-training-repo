<?php
/**
 * Test Softrip sync suite.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use Quark\Tests\Softrip\Softrip_TestCase;
use WP_Query;

use const Quark\Itineraries\POST_TYPE AS ITINERARY_POST_TYPE;

/**
 * Class Test_Softrip_Sync
 */
class Test_Softrip_Sync extends Softrip_TestCase {

    /**
     * Class instance to use for the test.
     *
     * @var Softrip_Sync
     */
    protected $instance;

    /**
	 * Set up the class which will be tested.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		$this->instance = new Softrip_Sync();
	}

    /**
     * Test case for batching requests.
     *
     * @covers \Quark\Softrip\Softrip_Sync::batch_request()
     *
     * @return void
     */
    public function test_batch_request(): void {
        // Setup mock response.
        add_filter('pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3);

        // Test case 1: No argument passed.
        $result = $this->instance->batch_request();
        $this->assertEmpty($result);

        // Test case 2: Empty array passed.
        $result = $this->instance->batch_request([]);
        $this->assertEmpty($result);

        // Test case 3: Test code array with more than 5 elements.
        $test_codes = [
            'ABC-123',
            'DEF-456',
            'GHI-789',
            'JKL-012',
            'MNO-345',
            'PQR-678',
        ];
        $result = $this->instance->batch_request($test_codes);
        $this->assertEmpty($result);

        // Test case 4: Test code array with one element.
        $test_codes = ['ABC-123'];
        $result = $this->instance->batch_request($test_codes);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('ABC-123', $result);

        // Cleanup.
        remove_filter('pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request');
    }

    /**
     * Test for preparing batch ids.
     *
     * @covers \Quark\Softrip\Softrip_Sync::prepare_batch_ids()
     *
     * @return void
     */
    public function test_prepare_batch_ids(): void {
        // Test case 1: No argument passed.
        $result = $this->instance->prepare_batch_ids();
        $this->assertIsArray($result);
        $this->assertEmpty($result);

        // Test case 2: Empty array passed.
        $result = $this->instance->prepare_batch_ids([]);
        $this->assertIsArray($result);
        $this->assertEmpty($result);

        // Setup test data.
        $itineraries = new WP_Query([
            'post_type'      => ITINERARY_POST_TYPE,
            'posts_per_page' => 10,
            'fields'         => 'ids',
        ]);
        $itinerary_ids = $itineraries->posts;
        $this->assertIsArray( $itinerary_ids );
        $itinerary_ids = array_map( 'absint', $itinerary_ids );


        // Get the Softrip package ids.
        $softrip_package_ids = [];
        foreach ( $itinerary_ids as $id ) {
            $softrip_package_id = strval( get_post_meta( $id, 'softrip_package_id', true ) );
            $softrip_package_ids[] = $softrip_package_id;
        }

        // Test case 3: Test with 2 itinerary ids and a batch of 5.
        $itinerary_ids1 = array_slice( $itinerary_ids, 0, 5 );
        $softrip_package_ids1 = array_slice( $softrip_package_ids, 0, 5 );
        $result = $this->instance->prepare_batch_ids($itinerary_ids1);
        $this->assertIsArray($result);
        $expected = array_chunk($softrip_package_ids1, 5);
        $this->assertEquals($expected, $result);

        // Test case 4: Test with 5 itinerary ids and default batch size - equal ids count and batch size.
        $softrip_package_ids2 = array_slice( $softrip_package_ids, 0, 5 );
        $itinerary_ids2 = array_slice( $itinerary_ids, 0, 5 );
        $result = $this->instance->prepare_batch_ids($itinerary_ids2);
        $this->assertIsArray($result);
        $expected = array_chunk($softrip_package_ids2, 5);
        $this->assertEquals($expected, $result);

        // Test case 5: Test with 10 itinerary ids and a batch of 6.
        $softrip_package_ids3 = array_slice( $softrip_package_ids, 0, 10 );
        $itinerary_ids3 = array_slice( $itinerary_ids, 0, 10 );
        $result = $this->instance->prepare_batch_ids($itinerary_ids3, 6);
        $this->assertIsArray($result);
        $expected = array_chunk($softrip_package_ids3, 6);
        $this->assertEquals($expected, $result);

        // Test case 6: Test with 10 itinerary ids and a batch of 0 - fallback to 1 batch size.
        $result = $this->instance->prepare_batch_ids($itinerary_ids3, 0);
        $softrip_package_ids3 = array_slice( $softrip_package_ids, 0, 10 );
        $this->assertIsArray($result);
        $expected = array_chunk($softrip_package_ids3, 1);
        $this->assertEquals($expected, $result);

        // Test case 7: Test with a post without softrip package id.
        $itinerary_ids4 = $this->factory()->post->create_many(2, [
            'post_type' => ITINERARY_POST_TYPE,
        ]);
        $this->assertIsArray($itinerary_ids4);
        $itinerary_ids4 = array_map('absint', $itinerary_ids4);
        $result = $this->instance->prepare_batch_ids($itinerary_ids4);
        $this->assertIsArray($result);

        // Cleanup.
        foreach ( $itinerary_ids4 as $id ) {
            wp_delete_post($id, true);
        }
    }

    /**
     * Test for getting all itinerary ids.
     *
     * @covers \Quark\Softrip\Softrip_Sync::get_all_itinerary_ids()
     *
     * @return void
     */
    public function test_get_all_itinerary_ids(): void {
        // Test case 1: Test with 5 itinerary posts.
        $itinerary_ids = $this->factory()->post->create_many(5, [
            'post_type' => ITINERARY_POST_TYPE,
        ]);
        $this->assertIsArray($itinerary_ids);
        $itinerary_ids = array_map('absint', $itinerary_ids);
        $result = $this->instance->get_all_itinerary_ids();
        $this->assertIsArray($result);
        $this->assertEquals( count( array_intersect( $result, $itinerary_ids ) ), count( $itinerary_ids ) );

        // Cleanup.
        foreach ( $itinerary_ids as $id ) {
            wp_delete_post($id, true);
        }
    }
}