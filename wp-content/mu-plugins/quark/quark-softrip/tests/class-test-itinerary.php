<?php
/**
 * Test suite for Itinerary.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests;

use Quark\Softrip\Itinerary;
use Quark\Tests\Softrip\Softrip_TestCase;
use WP_Post;
use WP_Query;

use const Quark\Departures\CACHE_GROUP as DEPARTURE_CACHE_GROUP;
use const Quark\Departures\CACHE_KEY as DEPARTURE_CACHE_KEY;
use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;
use const Quark\Itineraries\POST_TYPE as ITINERARY_POST_TYPE;
use const Quark\Ships\POST_TYPE as SHIP_POST_TYPE;

class Test_Itinerary extends Softrip_TestCase {

    /**
     * Test get id.
     *
     * @covers \Quark\Softrip\Itinerary::get_id()
     *
     * @return void
     */
    public function test_get_id(): void {
        // Create a test itinerary post.
        $post = $this->factory()->post->create_and_get([
            'post_title' => 'Test Itinerary',
            'post_content' => 'Itinerary content',
            'post_type' => ITINERARY_POST_TYPE,
            'meta_input' => [
                'test_meta' => 1,
                'softrip_package_id' => 'UNQ-123',
            ],
        ]);

        $this->assertTrue( $post instanceof WP_Post );

        // Create an instance of Itinerary with empty post ID.
        $itinerary = new Itinerary();
        $id = $itinerary->get_id();
        $this->assertEquals( 0, $id );

        // Create an instance of Itinerary.
        $itinerary = new Itinerary( $post->ID );
        $id = $itinerary->get_id();
        $this->assertEquals( $post->ID, $id );

        // Cleanup.
        wp_delete_post( $post->ID, true );
        unset( $itinerary );
    }

    /**
     * Test is valid.
     *
     * @covers \Quark\Softrip\Itinerary::is_valid()
     *
     * @return void
     */
    public function test_is_valid(): void {
        // Create a test itinerary post.
        $post = $this->factory()->post->create_and_get([
            'post_title' => 'Test Itinerary',
            'post_content' => 'Itinerary content',
            'post_type' => ITINERARY_POST_TYPE,
            'meta_input' => [
                'test_meta' => 1,
                'softrip_package_id' => 'UNQ-123',
            ],
        ]);

        $this->assertTrue( $post instanceof WP_Post );

        // Create an instance of Itinerary with empty post ID.
        $itinerary = new Itinerary();
        $valid = $itinerary->is_valid();
        $this->assertFalse( $valid );

        // Create an instance of Itinerary.
        $itinerary = new Itinerary( $post->ID );
        $valid = $itinerary->is_valid();
        $this->assertTrue( $valid );

        // Cleanup.
        wp_delete_post( $post->ID, true );
        unset( $itinerary );
    }

    /**
     * Test get data.
     *
     * @covers \Quark\Softrip\Itinerary::get_data()
     *
     * @return void
     */
    public function test_get_data(): void {
        // Create a test itinerary post.
        $post = $this->factory()->post->create_and_get([
            'post_title' => 'Test Itinerary',
            'post_content' => 'Itinerary content',
            'post_type' => ITINERARY_POST_TYPE,
            'meta_input' => [
                'test_meta' => 1,
                'softrip_package_id' => 'UNQ-123',
            ],
        ]);

        $this->assertTrue( $post instanceof WP_Post );

        // Create an instance of Itinerary with empty post ID.
        $itinerary = new Itinerary();
        $data = $itinerary->get_data();
        $this->assertEmpty( $data );

        // Create an instance of Itinerary.
        $itinerary = new Itinerary( $post->ID );
        $data = $itinerary->get_data();
        $this->assertEquals( $post, $data['post'] );
        $this->assertEquals( 1, $data['post_meta']['test_meta'] );

        // Cleanup.
        wp_delete_post( $post->ID, true );
        unset( $itinerary );
    }

    /**
     * Test get post meta.
     *
     * @covers \Quark\Softrip\Itinerary::get_post_meta()
     *
     * @return void
     */
    public function test_get_post_meta(): void {
        // Create a test itinerary post.
        $post = $this->factory()->post->create_and_get([
            'post_title' => 'Test Itinerary',
            'post_content' => 'Itinerary content',
            'post_type' => ITINERARY_POST_TYPE,
            'meta_input' => [
                'test_meta' => 1,
                'softrip_package_id' => 'UNQ-123',
            ],
        ]);

        $this->assertTrue( $post instanceof WP_Post );

        // Create an instance of Itinerary with empty post ID.
        $itinerary = new Itinerary();
        $all_data = $itinerary->get_post_meta();
        $this->assertEmpty( $all_data );

        // Create an instance of Itinerary.
        $itinerary = new Itinerary( $post->ID );
        $all_data = $itinerary->get_post_meta();
        $single = $itinerary->get_post_meta( 'test_meta' );
        $invalid = $itinerary->get_post_meta( 'nothing' );

        $this->assertEquals(
            [
                'test_meta' => 1,
                'softrip_package_id' => 'UNQ-123',
            ],
            $all_data
        );
        $this->assertEquals( 1, $single );
        $this->assertEmpty( $invalid );

        // Cleanup.
        wp_delete_post( $post->ID, true );
        unset( $itinerary );
    }

    /**
     * Test get status.
     *
     * @covers \Quark\Softrip\Itinerary::get_status()
     *
     * @return void
     */
    public function test_get_status(): void {
        // Create a test itinerary post.
        $post = $this->factory()->post->create_and_get([
            'post_title' => 'Test Itinerary',
            'post_content' => 'Itinerary content',
            'post_type' => ITINERARY_POST_TYPE,
            'meta_input' => [
                'test_meta' => 1,
                'softrip_package_id' => 'UNQ-123',
            ],
        ]);

        $this->assertTrue( $post instanceof WP_Post );

        // Create an instance of Itinerary with empty post ID.
        $itinerary = new Itinerary();
        $status = $itinerary->get_status();
        $this->assertSame( 'draft', $status );

        // Create an instance of Itinerary.
        $itinerary = new Itinerary( $post->ID );
        $status = $itinerary->get_status();
        $this->assertSame( 'publish', $status );

        // Cleanup.
        wp_delete_post( $post->ID, true );
        unset( $itinerary );
    }

    /**
     * Test get departures.
     *
     * @covers \Quark\Softrip\Itinerary::get_departures()
     *
     * @return void
     */
    public function test_get_departures(): void {
        // Create a test itinerary post.
        $post = $this->factory()->post->create_and_get([
            'post_title' => 'Test Itinerary',
            'post_content' => 'Itinerary content',
            'post_type' => ITINERARY_POST_TYPE,
            'meta_input' => [
                'test_meta' => 1,
                'softrip_package_id' => 'UNQ-123',
            ],
        ]);

        $this->assertTrue( $post instanceof WP_Post );

        // Create an instance of Itinerary with empty post ID.
        $itinerary = new Itinerary();
        $departures = $itinerary->get_departures();
        $this->assertEmpty( $departures );

        // Create an instance of Itinerary with post ID which has no departures.
        $itinerary = new Itinerary( $post->ID );
        $departures = $itinerary->get_departures();
        $this->assertEmpty( $departures );

        // Create some test departures without any departure_unique_id.
        $departure1 = $this->factory()->post->create_and_get([
            'post_title' => 'Test Departure 1',
            'post_content' => 'Departure content 1',
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'publish',
            'post_parent' => $post->ID,
        ]);
        $this->assertTrue( $departure1 instanceof WP_Post );

        $departure2 = $this->factory()->post->create_and_get([
            'post_title' => 'Test Departure 2',
            'post_content' => 'Departure content 2',
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'publish',
            'post_parent' => $post->ID,
        ]);
        $this->assertTrue( $departure2 instanceof WP_Post );

        // Draft departure.
        $departure3 = $this->factory()->post->create_and_get([
            'post_title' => 'Test Departure 3',
            'post_content' => 'Departure content 3',
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'draft',
            'post_parent' => $post->ID,
        ]);
        $this->assertTrue( $departure3 instanceof WP_Post );

        // Trashed Departure.
        $departure4 = $this->factory()->post->create_and_get([
            'post_title' => 'Test Departure 4',
            'post_content' => 'Departure content 4',
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'trash',
            'post_parent' => $post->ID,
        ]);
        $this->assertTrue( $departure4 instanceof WP_Post );

        // Create an instance of Itinerary.
        $itinerary = new Itinerary( $post->ID );
        $departures = $itinerary->get_departures();
        $this->assertEmpty( $departures );

        // Set departure_unique_id meta.
        update_post_meta( $departure1->ID, 'departure_unique_id', 'UNQ-123:2024-05-19' );
        update_post_meta( $departure2->ID, 'departure_unique_id', 'UNQ-123:2024-05-26' );
        update_post_meta( $departure3->ID, 'departure_unique_id', 'UNQ-123:2024-06-02' );
        update_post_meta( $departure4->ID, 'departure_unique_id', 'UNQ-123:2024-06-09' );
        
        // Bust cache.
        wp_cache_delete( DEPARTURE_CACHE_KEY . "_$departure1->ID", DEPARTURE_CACHE_GROUP );
        wp_cache_delete( DEPARTURE_CACHE_KEY . "_$departure2->ID", DEPARTURE_CACHE_GROUP );
        wp_cache_delete( DEPARTURE_CACHE_KEY . "_$departure3->ID", DEPARTURE_CACHE_GROUP );

        // Create an instance of Itinerary.
        $itinerary = new Itinerary( $post->ID );
        $departures = $itinerary->get_departures();
        $this->assertIsArray( $departures );
        $this->assertNotEmpty( $departures );
        $this->assertCount( 3, $departures );
        
        // Check if correct departures are loaded.
        $this->assertArrayHasKey( 'UNQ-123:2024-05-19', $departures );
        $this->assertArrayHasKey( 'UNQ-123:2024-05-26', $departures );
        $this->assertArrayHasKey( 'UNQ-123:2024-06-02', $departures );
        $this->assertArrayNotHasKey( 'UNQ-123:2024-06-09', $departures ); // trashed departure.

        // Check if each departure is an instance of Departure.
        foreach ( $departures as $departure ) {
            $this->assertInstanceOf( 'Quark\Softrip\Departure', $departure );
        }

        // Cleanup.
        wp_delete_post( $post->ID, true );
        wp_delete_post( $departure1->ID, true );
        wp_delete_post( $departure2->ID, true );
        wp_delete_post( $departure3->ID, true );
        wp_delete_post( $departure4->ID, true );
        unset( $itinerary );
    }

    /**
     * Test get published departures.
     *
     * @covers \Quark\Softrip\Itinerary::get_published_departures()
     *
     * @return void
     */
    public function test_get_published_departures(): void {
        // Create a test itinerary post.
        $post = $this->factory()->post->create_and_get([
            'post_title' => 'Test Itinerary',
            'post_content' => 'Itinerary content',
            'post_type' => ITINERARY_POST_TYPE,
            'meta_input' => [
                'test_meta' => 1,
                'softrip_package_id' => 'UNQ-123',
            ],
        ]);

        $this->assertTrue( $post instanceof WP_Post );

        // Create an instance of Itinerary with empty post ID.
        $itinerary = new Itinerary();
        $departures = $itinerary->get_published_departures();
        $this->assertEmpty( $departures );

        // Create an instance of Itinerary with post ID which has no departures.
        $itinerary = new Itinerary( $post->ID );
        $departures = $itinerary->get_published_departures();
        $this->assertEmpty( $departures );

        // Create some test departures without any departure_unique_id.
        $departure1 = $this->factory()->post->create_and_get([
            'post_title' => 'Test Departure 1',
            'post_content' => 'Departure content 1',
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'publish',
            'post_parent' => $post->ID,
        ]);
        $this->assertTrue( $departure1 instanceof WP_Post );

        $departure2 = $this->factory()->post->create_and_get([
            'post_title' => 'Test Departure 2',
            'post_content' => 'Departure content 2',
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'publish',
            'post_parent' => $post->ID,
        ]);
        $this->assertTrue( $departure2 instanceof WP_Post );

        // Draft departure.
        $departure3 = $this->factory()->post->create_and_get([
            'post_title' => 'Test Departure 3',
            'post_content' => 'Departure content 3',
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'draft',
            'post_parent' => $post->ID,
        ]);
        $this->assertTrue( $departure3 instanceof WP_Post );

        // Trashed Departure
        $departure4 = $this->factory()->post->create_and_get([
            'post_title' => 'Test Departure 4',
            'post_content' => 'Departure content 4',
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'trash',
            'post_parent' => $post->ID,
        ]);
        $this->assertTrue( $departure4 instanceof WP_Post );

        // Create an instance of Itinerary - departures not provided with departure_unique_id.
        $itinerary = new Itinerary( $post->ID );
        $departures = $itinerary->get_published_departures();
        $this->assertEmpty( $departures );

        // Set departure_unique_id meta.
        update_post_meta( $departure1->ID, 'departure_unique_id', 'UNQ-123:2024-05-19' );
        update_post_meta( $departure2->ID, 'departure_unique_id', 'UNQ-123:2024-05-26' );
        update_post_meta( $departure3->ID, 'departure_unique_id', 'UNQ-123:2024-06-02' );
        update_post_meta( $departure4->ID, 'departure_unique_id', 'UNQ-123:2024-06-09' );

        // Bust cache.
        wp_cache_delete( DEPARTURE_CACHE_KEY . "_$departure1->ID", DEPARTURE_CACHE_GROUP );
        wp_cache_delete( DEPARTURE_CACHE_KEY . "_$departure2->ID", DEPARTURE_CACHE_GROUP );
        wp_cache_delete( DEPARTURE_CACHE_KEY . "_$departure3->ID", DEPARTURE_CACHE_GROUP );
        wp_cache_delete( DEPARTURE_CACHE_KEY . "_$departure4->ID", DEPARTURE_CACHE_GROUP );

        // Create an instance of Itinerary.
        $itinerary = new Itinerary( $post->ID );
        $departures = $itinerary->get_published_departures();
        $this->assertIsArray( $departures );
        $this->assertNotEmpty( $departures );
        $this->assertCount( 2, $departures );
        
        // Check if correct departures are loaded.
        $this->assertArrayHasKey( 'UNQ-123:2024-05-19', $departures );
        $this->assertArrayHasKey( 'UNQ-123:2024-05-26', $departures );
        $this->assertArrayNotHasKey( 'UNQ-123:2024-06-02', $departures ); // draft departure.
        $this->assertArrayNotHasKey( 'UNQ-123:2024-06-09', $departures ); // trashed departure.

        // Check if each departure is an instance of Departure.
        foreach ( $departures as $departure ) {
            $this->assertInstanceOf( 'Quark\Softrip\Departure', $departure );
        }

        // Cleanup.
        wp_delete_post( $post->ID, true );
        wp_delete_post( $departure1->ID, true );
        wp_delete_post( $departure2->ID, true );
        wp_delete_post( $departure3->ID, true );
        wp_delete_post( $departure4->ID, true );
        unset( $itinerary );
    }

    /**
     * Test get a departure.
     *
     * @covers \Quark\Softrip\Itinerary::get_departure()
     *
     * @return void
     */
    public function test_get_departure(): void {
        // Create a test itinerary post.
        $post = $this->factory()->post->create_and_get([
            'post_title' => 'Test Itinerary',
            'post_content' => 'Itinerary content',
            'post_type' => ITINERARY_POST_TYPE,
            'meta_input' => [
                'test_meta' => 1,
                'softrip_package_id' => 'UNQ-123',
            ],
        ]);

        $this->assertTrue( $post instanceof WP_Post );

        // Create an instance of Itinerary with no departure id.
        $itinerary = new Itinerary();
        $departure = $itinerary->get_departure();
        $this->assertNull( $departure );

        // Create an instance of Itinerary without post ID but provide departure id so that new departure could be inserted.
        $itinerary = new Itinerary();
        $departure = $itinerary->get_departure( 'UNQ-123:2024-05-19' );
        $this->assertNotNull( $departure );
        $this->assertInstanceOf( 'Quark\Softrip\Departure', $departure );

        // Create an instance of Itinerary with post ID which has no departures.
        $itinerary = new Itinerary( $post->ID );
        $departure = $itinerary->get_departure( 'UNQ-123:2024-05-19' );
        $this->assertNotNull( $departure );
        $this->assertInstanceOf( 'Quark\Softrip\Departure', $departure );

        // Create some test departures without any departure_unique_id.
        $departure1 = $this->factory()->post->create_and_get([
            'post_title' => 'Test Departure 1',
            'post_content' => 'Departure content 1',
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'publish',
            'post_parent' => $post->ID,
        ]);
        $this->assertTrue( $departure1 instanceof WP_Post );

        $departure2_code = 'UNQ-123:2024-05-26';
        $departure2 = $this->factory()->post->create_and_get([
            'post_title' => 'Test Departure 2',
            'post_content' => 'Departure content 2',
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'publish',
            'post_parent' => $post->ID,
            'meta_input' => [
                'departure_unique_id' => $departure2_code,
            ],
        ]);
        $this->assertTrue( $departure2 instanceof WP_Post );

        // Draft departure.
        $departure3_code = 'UNQ-123:2024-06-02';
        $departure3 = $this->factory()->post->create_and_get([
            'post_title' => 'Test Departure 3',
            'post_content' => 'Departure content 3',
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'draft',
            'post_parent' => $post->ID,
            'meta_input' => [
                'departure_unique_id' => $departure3_code,
            ],
        ]);
        $this->assertTrue( $departure3 instanceof WP_Post );

        // Trashed Departure.
        $departure4_code = 'UNQ-123:2024-06-09';
        $departure4 = $this->factory()->post->create_and_get([
            'post_title' => 'Test Departure 4',
            'post_content' => 'Departure content 4',
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'trash',
            'post_parent' => $post->ID,
            'meta_input' => [
                'departure_unique_id' => $departure4_code,
            ],
        ]);
        $this->assertTrue( $departure4 instanceof WP_Post );

        // Create an instance of Itinerary.
        $itinerary = new Itinerary( $post->ID );

        // New departure.
        $departure = $itinerary->get_departure( 'UNQ-123:2024-05-19' );
        $this->assertInstanceOf( 'Quark\Softrip\Departure', $departure );
        $departure_data = $departure->get_data();
        $this->assertIsArray( $departure_data );
        $this->assertEmpty( $departure_data );

        // Existent departures - published.
        $departure = $itinerary->get_departure( $departure2_code );
        $this->assertInstanceOf( 'Quark\Softrip\Departure', $departure );
        $departure_data = $departure->get_data();
        $this->assertIsArray( $departure_data );
        $this->assertArrayHasKey( 'post', $departure_data );
        $this->assertTrue( $departure_data['post'] instanceof WP_Post );
        $this->assertEquals( $departure2->ID, $departure_data['post']->ID );

        // Draft departure.
        $departure = $itinerary->get_departure( $departure3_code );
        $this->assertInstanceOf( 'Quark\Softrip\Departure', $departure );
        $departure_data = $departure->get_data();
        $this->assertIsArray( $departure_data );
        $this->assertArrayHasKey( 'post', $departure_data );
        $this->assertTrue( $departure_data['post'] instanceof WP_Post );
        $this->assertEquals( $departure3->ID, $departure_data['post']->ID );

        // Trashed departure.
        $departure = $itinerary->get_departure( $departure4_code );
        $this->assertInstanceOf( 'Quark\Softrip\Departure', $departure );
        $departure_data = $departure->get_data();
        $this->assertIsArray( $departure_data );
        $this->assertEmpty( $departure_data );

        // Cleanup.
        wp_delete_post( $post->ID, true );
        wp_delete_post( $departure1->ID, true );
        wp_delete_post( $departure2->ID, true );
        wp_delete_post( $departure3->ID, true );
        wp_delete_post( $departure4->ID, true );
        unset( $itinerary );
    }

    /**
     * Test update departures.
     *
     * @covers \Quark\Softrip\Itinerary::update_departures()
     *
     * @return void
     */
    public function test_update_departures(): void {
        // Create a test itinerary post.
        $post = $this->factory()->post->create_and_get([
            'post_title' => 'Test Itinerary',
            'post_content' => 'Itinerary content',
            'post_type' => ITINERARY_POST_TYPE,
            'meta_input' => [
                'test_meta' => 1,
                'softrip_package_id' => 'UNQ-123',
            ],
        ]);
        $this->assertTrue( $post instanceof WP_Post );

        // Create an instance of Itinerary with empty post ID.
        $itinerary = new Itinerary();
        $itinerary->update_departures();
        $departures = $itinerary->get_departures();
        $this->assertEmpty( $departures );

        // Create an instance of Itinerary with post ID which has no departures.
        $itinerary = new Itinerary( $post->ID );
        $itinerary->update_departures();
        $departures = $itinerary->get_departures();
        $this->assertEmpty( $departures );

        // Create some test departures without any departure_unique_id.
        $departure1 = $this->factory()->post->create_and_get([
            'post_title' => 'Test Departure 1',
            'post_content' => 'Departure content 1',
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'publish',
            'post_parent' => $post->ID,
        ]);
        $this->assertTrue( $departure1 instanceof WP_Post );

        $departure2_code = 'UNQ-123:2024-05-26';
        $departure2 = $this->factory()->post->create_and_get([
            'post_title' => 'Test Departure 2',
            'post_content' => 'Departure content 2',
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'publish',
            'post_parent' => $post->ID,
            'meta_input' => [
                'departure_unique_id' => $departure2_code,
            ],
        ]);
        $this->assertTrue( $departure2 instanceof WP_Post );

        // Draft departure.
        $departure3_code = 'UNQ-123:2024-06-02';
        $departure3 = $this->factory()->post->create_and_get([
            'post_title' => 'Test Departure 3',
            'post_content' => 'Departure content 3',
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'draft',
            'post_parent' => $post->ID,
            'meta_input' => [
                'departure_unique_id' => $departure3_code,
            ],
        ]);
        $this->assertTrue( $departure3 instanceof WP_Post );

        // Create an instance of Itinerary.
        $itinerary = new Itinerary( $post->ID );

        // Departure 2.
        $departure2_obj = $itinerary->get_departure( $departure2_code );
        $this->assertInstanceOf( 'Quark\Softrip\Departure', $departure2_obj );
        $departure2_data = $departure2_obj->get_data();
        $this->assertIsArray( $departure2_data );
        $this->assertArrayHasKey( 'post_meta', $departure2_data );
        $this->assertIsArray( $departure2_data['post_meta'] );
        $this->assertArrayHasKey( 'departure_unique_id', $departure2_data['post_meta'] );
        $this->assertEquals( $departure2_code, $departure2_data['post_meta']['departure_unique_id'] );
        $this->assertArrayNotHasKey( 'departure_start_date', $departure2_data['post_meta'] );
        $this->assertArrayNotHasKey( 'departure_end_date', $departure2_data['post_meta'] );
        $this->assertArrayNotHasKey( 'duration', $departure2_data['post_meta'] );

        // Departure 3.
        $departure3_obj = $itinerary->get_departure( $departure3_code );
        $this->assertInstanceOf( 'Quark\Softrip\Departure', $departure3_obj );
        $this->assertEmpty( $departure3_obj->get_post_meta( 'departure_start_date' ) );
        $this->assertEmpty( $departure3_obj->get_post_meta( 'departure_end_date' ) );
        $this->assertEmpty( $departure3_obj->get_post_meta( 'duration' ) );


        // Raw departure data.
        $raw_departure_data = [
            'departures' => [
                [
                    'id' => $departure2_code,
                    "startDate" => "2025-05-19",
                    "endDate" => "2025-05-25",
                    "duration" =>  7,
                ],
            ]
        ];

        // Update departures.
        $itinerary->update_departures( $raw_departure_data );

        // Get departure again.
        $updated_departure2_data = $departure2_obj->get_data();
        $this->assertIsArray( $updated_departure2_data );
        $this->assertArrayHasKey( 'post_meta', $updated_departure2_data );
        $this->assertSame($departure2_obj->get_post_meta('departure_start_date'), $raw_departure_data['departures'][0]['startDate']);
        $this->assertSame($departure2_obj->get_post_meta('departure_end_date'), $raw_departure_data['departures'][0]['endDate']);
        $this->assertSame($departure2_obj->get_post_meta('duration'), strval( $raw_departure_data['departures'][0]['duration']) );

        // Cleanup.
        wp_delete_post( $post->ID, true );
        wp_delete_post( $departure1->ID, true );
        wp_delete_post( $departure2->ID, true );
        wp_delete_post( $departure3->ID, true );
        unset( $itinerary );

        /**
         * Verify with real Softrip data.
         */
        $post = $this->factory()->post->create_and_get([
            'post_title' => 'Test Itinerary',
            'post_content' => 'Itinerary content',
            'post_type' => ITINERARY_POST_TYPE,
            'meta_input' => [
                'test_meta' => 1,
                'softrip_package_id' => 'ABC-123',
            ],
        ]);
        $this->assertTrue( $post instanceof WP_Post );

        // No departure posts should be existing.
        $departure_posts = new WP_Query([
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'publish',
            'post_parent' => $post->ID,
            'posts_per_page' => -1,
            'no_found_row'   => true,
        ]);
        $this->assertEquals( 0, $departure_posts->found_posts );

        // Create an instance of Itinerary. Currently, it should have no departure.
        $itinerary = new Itinerary( $post->ID );
        $old_departures = $itinerary->get_departures();
        $this->assertIsArray( $old_departures );
        $this->assertEmpty( $old_departures );

        /**
         * Update departures with real Softrip data.
         * Since, we are not providing any raw departure in argument, it should fetch departures from Softrip API.
         * 1. Add filter to mock request.
         * 2. Update departures.
         * 3. Remove filter.
         * 4. Get departures.
         * 5. Assert that departures are fetched and saved correctly.
         */
        add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );
        $itinerary->update_departures();
        remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10 );

        // Verify.
        $new_departures = $itinerary->get_departures();
        $this->assertIsArray( $new_departures );
        $this->assertNotEmpty( $new_departures );

        // Old and new departures should not be same.
        $this->assertNotEquals( $old_departures, $new_departures );

        // New departure posts should have been created.
        $new_departure_posts = new WP_Query([
            'post_type' => DEPARTURE_POST_TYPE,
            'post_status' => 'publish',
            'post_parent' => $post->ID,
            'posts_per_page' => -1,
            'no_found_row'   => true,
        ]);
        $this->assertGreaterThan( 0, $new_departure_posts->found_posts );

        // New departures variables should contain new departure instances.
        foreach ( $new_departures as $new_departure ) {
            $this->assertInstanceOf( 'Quark\Softrip\Departure', $new_departure );
        }

        // Check if correct departures are loaded.
        $this->assertArrayNotHasKey( 'ABC-123:2026-02-28', $old_departures );
		$this->assertArrayHasKey( 'ABC-123:2026-02-28', $new_departures );

        // Cleanup.
        wp_delete_post( $post->ID, true );
        foreach ( $new_departure_posts->posts as $new_departure_post ) {
            // Check if post is valid.
            if ( ! $new_departure_post instanceof WP_Post ) {
                continue;
            }

            // Delete post.
            wp_delete_post( $new_departure_post->ID, true );
        }
        unset( $itinerary );
    }

    /**
     * Test get lowest price.
     *
     * @covers \Quark\Softrip\Itinerary::get_lowest_price()
     *
     * @return void
     */
    public function test_get_lowest_price(): void {
        // Create a test itinerary post.
        $post = $this->factory()->post->create_and_get([
            'post_title' => 'Test Itinerary',
            'post_content' => 'Itinerary content',
            'post_type' => ITINERARY_POST_TYPE,
            'meta_input' => [
                'test_meta' => 1,
                'softrip_package_id' => 'UNQ-123',
            ],
        ]);
        $this->assertTrue( $post instanceof WP_Post );

        // Create an instance of Itinerary.
        $itinerary = new Itinerary( $post->ID );

        // No departures currently.
        $lowest_price = $itinerary->get_lowest_price();
        $this->assertIsFloat( $lowest_price );
        $this->assertEquals( 0, $lowest_price );

        // Departure data.
        $departure_data = [
			'departures' => [
				[
					'id'          => 'UNQ-123:2026-02-28',
					'code'        => 'OEX20260228',
					'packageCode' => 'UNQ-123',
					'startDate'   => '2026-02-28',
					'endDate'     => '2026-03-11',
					'duration'    => 11,
					'shipCode'    => 'LOQ',
					'marketCode'  => 'ANT',
					'cabins'      => [
						[
							'id'          => 'UNQ-123:2026-02-28:LOQ-SGL',
							'code'        => 'LOQ-SGL',
							'name'        => 'Studio Single',
							'departureId' => 'UNQ-123:2026-02-28',
							'occupancies' => [
								[
									'id'                      => 'UNQ-123:2026-02-28:LOQ-SGL:A',
									'name'                    => 'UNQ-123:2026-02-28:LOQ-SGL:A',
									'mask'                    => 'A',
									'availabilityStatus'      => 'C',
									'availabilityDescription' => 'Unavailable',
									'spacesAvailable'         => 0,
									'seq'                     => '100',
									'prices'                  => [
										'USD' => [
											'pricePerPerson' => 34895,
											'currencyCode'   => 'USD',
											'promos'         => [
												'25PROMO' => [
													'promoPricePerPerson' => 26171,
												],
											],
										],
										'AUD' => [
											'pricePerPerson' => 54795,
											'currencyCode'   => 'AUD',
											'promos'         => [
												'25PROMO' => [
													'promoPricePerPerson' => 41096,
												],
											],
										],
										'CAD' => [
											'pricePerPerson' => 47495,
											'currencyCode'   => 'CAD',
											'promos'         => [
												'25PROMO' => [
													'promoPricePerPerson' => 35621,
												],
											],
										],
										'EUR' => [
											'pricePerPerson' => 32495,
											'currencyCode'   => 'EUR',
											'promos'         => [
												'25PROMO' => [
													'promoPricePerPerson' => 24371,
												],
											],
										],
										'GBP' => [
											'pricePerPerson' => 27995,
											'currencyCode'   => 'GBP',
											'promos'         => [
												'25PROMO' => [
													'promoPricePerPerson' => 20996,
												],
											],
										],
									],
								],
							],
						],
					],
				],
			],
		];

        // Expected lowest price - inferred from above departure data.
        $expected_lowest_price = [
            'USD' => 34895,
            'AUD' => 54795,
            'CAD' => 47495,
            'EUR' => 32495,
            'GBP' => 27995,
        ];

        // Update departures.
        $itinerary->update_departures( $departure_data );

        // Get lowest price without currency - default USD.
        $lowest_price = $itinerary->get_lowest_price();
        $this->assertIsFloat( $lowest_price );
        $this->assertEquals( $expected_lowest_price['USD'], $lowest_price );

        // Verify with each currency.
        foreach ( $expected_lowest_price as $currency => $price ) {
            $lowest_price = $itinerary->get_lowest_price( $currency );
            $this->assertIsFloat( $lowest_price );
            $this->assertEquals( $price, $lowest_price );
        }

        // Cleanup.
        wp_delete_post( $post->ID, true );
        foreach ( $itinerary->get_departures() as $departure ) {
            wp_delete_post( $departure->get_id(), true );
        }
        unset( $itinerary );
    }

    /**
     * Test get related ships.
     *
     * @covers \Quark\Softrip\Itinerary::get_related_ships()
     *
     * @return void
     */
    public function test_get_related_ships(): void {
        // Create a test itinerary post.
        $post = $this->factory()->post->create_and_get([
            'post_title' => 'Test Itinerary',
            'post_content' => 'Itinerary content',
            'post_type' => ITINERARY_POST_TYPE,
            'meta_input' => [
                'test_meta' => 1,
                'softrip_package_id' => 'UNQ-123',
            ],
        ]);
        $this->assertTrue( $post instanceof WP_Post );

        // Create some ships.
        $ship1 = $this->factory()->post->create_and_get([
            'post_title' => 'Ship 1',
            'post_content' => 'Ship 1 content',
            'post_type' => SHIP_POST_TYPE,
            'meta_input' => [
                'ship_id' => 'LOQ',
            ],
        ]);
        $this->assertTrue( $ship1 instanceof WP_Post );

        $ship2 = $this->factory()->post->create_and_get([
            'post_title' => 'Ship 2',
            'post_content' => 'Ship 2 content',
            'post_type' => SHIP_POST_TYPE,
            'meta_input' => [
                'ship_id' => 'LOQ2',
            ],
        ]);
        $this->assertTrue( $ship2 instanceof WP_Post );

        // Create an instance of Itinerary.
        $itinerary = new Itinerary( $post->ID );

        // No departures currently.
        $related_ships = $itinerary->get_related_ships();
        $this->assertEmpty( $related_ships );

        // Departure data.
        $departure_data = [
			'departures' => [
				[
					'id'          => 'UNQ-123:2026-02-28',
					'code'        => 'OEX20260228',
					'packageCode' => 'UNQ-123',
					'startDate'   => '2026-02-28',
					'endDate'     => '2026-03-11',
					'duration'    => 11,
					'shipCode'    => 'LOQ',
					'marketCode'  => 'ANT',
					'cabins'      => [
						[
							'id'          => 'UNQ-123:2026-02-28:LOQ-SGL',
							'code'        => 'LOQ-SGL',
							'name'        => 'Studio Single',
							'departureId' => 'UNQ-123:2026-02-28',
							'occupancies' => [
								[
									'id'                      => 'UNQ-123:2026-02-28:LOQ-SGL:A',
									'name'                    => 'UNQ-123:2026-02-28:LOQ-SGL:A',
									'mask'                    => 'A',
									'availabilityStatus'      => 'C',
									'availabilityDescription' => 'Unavailable',
									'spacesAvailable'         => 0,
									'seq'                     => '100',
									'prices'                  => [
										'USD' => [
											'pricePerPerson' => 34895,
											'currencyCode'   => 'USD',
											'promos'         => [
												'25PROMO' => [
													'promoPricePerPerson' => 26171,
												],
											],
										],
										'AUD' => [
											'pricePerPerson' => 54795,
											'currencyCode'   => 'AUD',
											'promos'         => [
												'25PROMO' => [
													'promoPricePerPerson' => 41096,
												],
											],
										],
										'CAD' => [
											'pricePerPerson' => 47495,
											'currencyCode'   => 'CAD',
											'promos'         => [
												'25PROMO' => [
													'promoPricePerPerson' => 35621,
												],
											],
										],
										'EUR' => [
											'pricePerPerson' => 32495,
											'currencyCode'   => 'EUR',
											'promos'         => [
												'25PROMO' => [
													'promoPricePerPerson' => 24371,
												],
											],
										],
										'GBP' => [
											'pricePerPerson' => 27995,
											'currencyCode'   => 'GBP',
											'promos'         => [
												'25PROMO' => [
													'promoPricePerPerson' => 20996,
												],
											],
										],
									],
								],
							],
						],
					],
				],
			],
		];

        // Update departures.
        $itinerary->update_departures( $departure_data );

        // Get related ships.
        $related_ships = $itinerary->get_related_ships();
        $this->assertIsArray( $related_ships );
        $this->assertNotEmpty( $related_ships );
        $this->assertCount( 1, $related_ships );
        $this->assertArrayHasKey( $ship1->ID, $related_ships );
        $this->assertArrayNotHasKey( $ship2->ID, $related_ships );
    }

    /**
     * Test get starting date.
     *
     * @covers \Quark\Softrip\Itinerary::get_starting_date()
     *
     * @return void
     */
    public function test_get_starting_date(): void {
        // Create a test itinerary post.
        $post = $this->factory()->post->create_and_get([
            'post_title' => 'Test Itinerary',
            'post_content' => 'Itinerary content',
            'post_type' => ITINERARY_POST_TYPE,
            'meta_input' => [
                'test_meta' => 1,
                'softrip_package_id' => 'UNQ-123',
            ],
        ]);
        $this->assertTrue( $post instanceof WP_Post );

        // Create an instance of Itinerary with empty post ID.
        $itinerary = new Itinerary();
        $starting_date = $itinerary->get_starting_date();
        $this->assertEmpty( $starting_date );

        // Create an instance of Itinerary.
        $itinerary = new Itinerary( $post->ID );

        // No departures currently.
        $starting_date = $itinerary->get_starting_date();
        $this->assertEmpty( $starting_date );

        // Departure data.
        $departure_data = [
            'departures' => [
                [
                    'id'          => 'UNQ-123:2026-02-28',
                    'code'        => 'OEX20260228',
                    'packageCode' => 'UNQ-123',
                    'startDate'   => '2026-02-28',
                    'endDate'     => '2026-03-11',
                    'duration'    => 11,
                    'shipCode'    => 'LOQ',
                    'marketCode'  => 'ANT',
                ],
            ],
        ];

        // Update departures.
        $itinerary->update_departures( $departure_data );

        // Get starting date.
        $starting_date = $itinerary->get_starting_date();
        $this->assertNotEmpty( $starting_date );
        $this->assertEquals( '2026-02-28', $starting_date );
    }

    /**
     * Test get ending date.
     *
     * @covers \Quark\Softrip\Itinerary::get_ending_date()
     *
     * @return void
     */
    public function test_get_ending_date(): void {
        // Create a test itinerary post.
        $post = $this->factory()->post->create_and_get([
            'post_title' => 'Test Itinerary',
            'post_content' => 'Itinerary content',
            'post_type' => ITINERARY_POST_TYPE,
            'meta_input' => [
                'test_meta' => 1,
                'softrip_package_id' => 'UNQ-123',
            ],
        ]);
        $this->assertTrue( $post instanceof WP_Post );

        // Create an instance of Itinerary with empty post ID.
        $itinerary = new Itinerary();
        $ending_date = $itinerary->get_ending_date();
        $this->assertEmpty( $ending_date );

        // Create an instance of Itinerary.
        $itinerary = new Itinerary( $post->ID );

        // No departures currently.
        $ending_date = $itinerary->get_ending_date();
        $this->assertEmpty( $ending_date );

        // Departure data.
        $departure_data = [
            'departures' => [
                [
                    'id'          => 'UNQ-123:2026-02-28',
                    'code'        => 'OEX20260228',
                    'packageCode' => 'UNQ-123',
                    'startDate'   => '2026-02-28',
                    'endDate'     => '2026-03-11',
                    'duration'    => 11,
                    'shipCode'    => 'LOQ',
                    'marketCode'  => 'ANT',
                ],
            ],
        ];

        // Update departures.
        $itinerary->update_departures( $departure_data );

        // Get ending date.
        $ending_date = $itinerary->get_ending_date();
        $this->assertNotEmpty( $ending_date );
        $this->assertEquals( '2026-03-11', $ending_date );
    }
}
