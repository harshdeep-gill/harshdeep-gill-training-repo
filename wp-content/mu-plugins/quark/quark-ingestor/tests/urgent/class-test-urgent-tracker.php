<?php
/**
 * Test suite for the Urgent tracker namespace.
 *
 * @package quark-ingestor
 */

namespace Quark\Ingestor\Tests;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\Ingestor\Urgent\dispatch_urgent_push_github_event;
use function Quark\Ingestor\Urgent\track_adventure_option_taxonomy_change;
use function Quark\Ingestor\Urgent\track_cabin_post_type_change;
use function Quark\Ingestor\Urgent\track_expedition_post_type_change;
use function Quark\Softrip\AdventureOptions\get_departures_by_adventure_option_term_id;
use function Quark\Softrip\do_sync;
use function Quark\Softrip\Occupancies\get_departures_by_cabin_category_id;

use const Quark\AdventureOptions\ADVENTURE_OPTION_CATEGORY;
use const Quark\CabinCategories\POST_TYPE as CABIN_POST_TYPE;
use const Quark\Expeditions\POST_TYPE as EXPEDITION_POST_TYPE;
use const Quark\Ingestor\Urgent\URGENTLY_CHANGED_EXPEDITION_IDS_OPTION;
use const Quark\Tests\Ingestor\TEST_IMAGE_PATH;

/**
 * Class Test_Urgent_Tracker
 *
 * @package quark-ingestor
 */
class Test_Urgent_Tracker extends Softrip_TestCase {
	/**
	 * GitHub API URL.
	 */
	const GH_API_URL = 'https://test.github-api.com'; // phpcs:ignore

	/**
	 * GitHub Action Token.
	 */
	const GH_ACTION_TOKEN = 'test-token'; // phpcs:ignore

	/**
	 * Github ref.
	 */
	const GH_REF = 'master'; // phpcs:ignore

	/**
	 * Dispatch data.
	 *
	 * @var mixed[]
	 */
	protected $dispatch_data = [];

	/**
	 * Test track expedition post type changes.
	 *
	 * @covers \Quark\Ingestor\Urgent\track_expedition_post_type_change
	 *
	 * @return void
	 */
	public function test_track_expedition_post_type_change(): void {
		// Test with no args.
		track_expedition_post_type_change();

		// Option should not be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertEmpty( $option_value );

		// Test with invalid post ID.
		track_expedition_post_type_change( 0 );

		// Option should not be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertEmpty( $option_value );

		// Create a post.
		$post_id = $this->factory()->post->create();
		$this->assertIsInt( $post_id );

		// Test with non-expedition post ID.
		track_expedition_post_type_change( $post_id );

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertEmpty( $option_value );

		// Create an expedition post.
		$expedition_post_id = $this->factory()->post->create( [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsInt( $expedition_post_id );

		// Test with expedition post ID.
		track_expedition_post_type_change( $expedition_post_id );

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertContains( $expedition_post_id, $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );

		// Test with multiple expedition post IDs.
		$expedition_post_ids = $this->factory()->post->create_many( 3, [ 'post_type' => EXPEDITION_POST_TYPE ] );
		$this->assertIsArray( $expedition_post_ids );

		// Validate post IDs.
		$this->assertIsInt( $expedition_post_ids[0] );
		$this->assertIsInt( $expedition_post_ids[1] );
		$this->assertIsInt( $expedition_post_ids[2] );

		// Test with multiple expedition post IDs.
		track_expedition_post_type_change( $expedition_post_ids[0] );
		track_expedition_post_type_change( $expedition_post_ids[1] );
		track_expedition_post_type_change( $expedition_post_ids[2] );

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertCount( 3, $option_value );
		$this->assertContains( $expedition_post_ids[0], $option_value );
		$this->assertContains( $expedition_post_ids[1], $option_value );
		$this->assertContains( $expedition_post_ids[2], $option_value );

		// Test with duplicate expedition post ID.
		track_expedition_post_type_change( $expedition_post_ids[0] );

		// Option should not be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertCount( 3, $option_value );
		$this->assertContains( $expedition_post_ids[0], $option_value );
		$this->assertContains( $expedition_post_ids[1], $option_value );
		$this->assertContains( $expedition_post_ids[2], $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );

		/**
		 * Test if on change of title, the tracker is triggered and option is saved.
		 */

		// Update title of an expedition post.
		wp_update_post(
			[
				'ID'         => $expedition_post_ids[0],
				'post_title' => 'Updated Title',
			]
		);

		// Do acf/save_post action.
		do_action( 'acf/save_post', $expedition_post_ids[0] ); // phpcs:ignore

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertCount( 1, $option_value );
		$this->assertContains( $expedition_post_ids[0], $option_value );

		// Do action again.
		do_action( 'acf/save_post', $expedition_post_ids[0] ); // phpcs:ignore

		// Option should not change - duplicate entry check.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertCount( 1, $option_value );
		$this->assertContains( $expedition_post_ids[0], $option_value );

		// Reset option.
		update_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION, [] );

		/**
		 * Test if on change of expedition image (hero slider image), the tracker is triggered and option is saved.
		 */

		// Create some media post.
		$media_post_id1 = $this->factory()->attachment->create_upload_object( TEST_IMAGE_PATH );
		$this->assertIsInt( $media_post_id1 );
		$media_post_id2 = $this->factory()->attachment->create_upload_object( TEST_IMAGE_PATH );
		$this->assertIsInt( $media_post_id2 );

		// Post content.
		$post_content = sprintf(
			'<!-- wp:paragraph -->
			<p>On this extraordinary journey, you will pack all the excitement of an epic Arctic cruise into just seven days, experience incredible Arctic wilderness you never dreamt possible. Though this rocky island is covered in mountains and glaciers, the towering cliffs and fjords play host to a thriving and diverse ecosystem. Exploring as much of the area as possible, you will enjoy maximum opportunities to spot, among other wildlife, the walrus with its long tusks and distinctive whiskers, the resilient and Arctic birds in all their varied majesty, and that most iconic of Arctic creatures, the polar bear.</p>
			<!-- /wp:paragraph -->

			<!-- wp:quark/expedition-hero -->
			<!-- wp:quark/expedition-hero-content -->
			<!-- wp:quark/expedition-hero-content-left -->
			<!-- wp:quark/expedition-details /-->
			<!-- /wp:quark/expedition-hero-content-left -->

			<!-- wp:quark/expedition-hero-content-right -->
			<!-- wp:quark/hero-card-slider {"items":[{"id":%1$s,"src":"%3$s","width":300,"height":200,"alt":"","caption":"","size":"medium"},{"id":%2$s,"src":"%4$s","width":300,"height":200,"alt":"","caption":"","size":"medium"},{"id":6592,"src":"https://local.quarkexpeditions.com/wp-content/uploads/2024/08/strote-jared-201809-214x300.jpg","width":214,"height":300,"alt":"","caption":"","size":"medium"},{"id":6594,"src":"https://local.quarkexpeditions.com/wp-content/uploads/2024/08/white-andrew-202102-300x200.jpg","width":300,"height":200,"alt":"","caption":"","size":"medium"}]} /-->
			<!-- /wp:quark/expedition-hero-content-right -->
			<!-- /wp:quark/expedition-hero-content -->
			<!-- /wp:quark/expedition-hero -->

			<!-- wp:quark/book-departures-expeditions /-->',
			$media_post_id1,
			$media_post_id2,
			wp_get_attachment_image_url( $media_post_id1, 'medium' ),
			wp_get_attachment_image_url( $media_post_id2, 'full' )
		);

		// Update post content.
		wp_update_post(
			[
				'ID'           => $expedition_post_ids[0],
				'post_content' => $post_content,
			]
		);

		// Do acf/save_post action.
		do_action( 'acf/save_post', $expedition_post_ids[0] ); // phpcs:ignore

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertCount( 1, $option_value );
		$this->assertContains( $expedition_post_ids[0], $option_value );

		// Do action again.
		do_action( 'acf/save_post', $expedition_post_ids[0] ); // phpcs:ignore

		// Option should not change - duplicate entry check.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertCount( 1, $option_value );
		$this->assertContains( $expedition_post_ids[0], $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
	}

	/**
	 * Test track cabin post type changes.
	 *
	 * @covers \Quark\Ingestor\Urgent\track_cabin_post_type_change
	 *
	 * @return void
	 */
	public function test_track_cabin_post_type_change(): void {
		// Test with no args.
		track_cabin_post_type_change();

		// Option should not be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertEmpty( $option_value );

		// Test with invalid post ID.
		track_cabin_post_type_change( 0 );

		// Option should not be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertEmpty( $option_value );

		// Create a post.
		$post_id = $this->factory()->post->create();
		$this->assertIsInt( $post_id );

		// Test with non-cabin post ID.
		track_cabin_post_type_change( $post_id );

		// Option should not be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertEmpty( $option_value );

		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Do sync.
		do_sync();

		// Remove filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10 );

		// Get a cabin category post.
		$cabin_category_post = get_posts(
			[
				'post_type'              => CABIN_POST_TYPE,
				'meta_key'               => 'cabin_category_id',
				'meta_value'             => 'OEX-SGL',
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);

		// Convert to int.
		$this->assertNotEmpty( $cabin_category_post );
		$cabin_category_post = array_map( 'absint', $cabin_category_post );
		$cabin_category_post = $cabin_category_post[0];

		// Test with cabin post ID.
		track_cabin_post_type_change( $cabin_category_post );

		// Get departures connected to this cabin.
		$departure_posts = get_departures_by_cabin_category_id( $cabin_category_post );
		$this->assertNotEmpty( $departure_posts );

		// Initialize array.
		$expedition_ids = [];

		// Get expedition IDs.
		foreach ( $departure_posts as $departure_post ) {
			$related_expedition_id = absint( get_post_meta( $departure_post, 'related_expedition', true ) );

			// Add to array if not exists.
			if ( ! in_array( $related_expedition_id, $expedition_ids, true ) ) {
				$expedition_ids[] = $related_expedition_id;
			}
		}

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertEquals( $expedition_ids, $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );

		// Update cabin post title.
		wp_update_post(
			[
				'ID'         => $cabin_category_post,
				'post_title' => 'Updated Title',
			]
		);

		// Do acf/save_post action.
		do_action( 'acf/save_post', $cabin_category_post ); // phpcs:ignore

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertEquals( $expedition_ids, $option_value );

		// Do action again.
		do_action( 'acf/save_post', $cabin_category_post ); // phpcs:ignore

		// Option should not change - duplicate entry check.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertEquals( $expedition_ids, $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );

		// Update cabin post content.
		wp_update_post(
			[
				'ID'           => $cabin_category_post,
				'post_content' => 'Updated Content',
			]
		);

		// Do acf/save_post action.
		do_action( 'acf/save_post', $cabin_category_post ); // phpcs:ignore

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertEquals( $expedition_ids, $option_value );

		// Do action again.
		do_action( 'acf/save_post', $cabin_category_post ); // phpcs:ignore

		// Option should not change - duplicate entry check.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertEquals( $expedition_ids, $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );

		// Update cabin post excerpt.
		wp_update_post(
			[
				'ID'           => $cabin_category_post,
				'post_excerpt' => 'Updated Excerpt',
			]
		);

		// Do acf/save_post action.
		do_action( 'acf/save_post', $cabin_category_post ); // phpcs:ignore

		// Option should be not be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertEmpty( $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );

		// Update cabin post status.
		wp_update_post(
			[
				'ID'          => $cabin_category_post,
				'post_status' => 'draft',
			]
		);

		// Do acf/save_post action.
		do_action( 'acf/save_post', $cabin_category_post ); // phpcs:ignore

		// Option should be not be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertEmpty( $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );

		// Create some media post.
		$media_post_id1 = $this->factory()->attachment->create_upload_object( TEST_IMAGE_PATH );
		$this->assertIsInt( $media_post_id1 );
		$media_post_id2 = $this->factory()->attachment->create_upload_object( TEST_IMAGE_PATH );
		$this->assertIsInt( $media_post_id2 );

		// Add cabin image ids in meta.
		wp_update_post(
			[
				'ID'         => $cabin_category_post,
				'meta_input' => [ 'cabin_images' => [ $media_post_id1 ] ],
			]
		);

		// Do acf/save_post action.
		do_action( 'acf/save_post', $cabin_category_post ); // phpcs:ignore

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertEquals( $expedition_ids, $option_value );

		// update cabin image ids in meta.
		wp_update_post(
			[
				'ID'         => $cabin_category_post,
				'meta_input' => [ 'cabin_images' => [ $media_post_id1, $media_post_id2 ] ],
			]
		);

		// Do acf/save_post action.
		do_action( 'acf/save_post', $cabin_category_post ); // phpcs:ignore

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertEquals( $expedition_ids, $option_value );

		// Get another cabin category post.
		$cabin_category_post = get_posts(
			[
				'post_type'              => CABIN_POST_TYPE,
				'meta_key'               => 'cabin_category_id',
				'meta_value'             => 'OEX-FWD',
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'ignore_sticky_posts'    => true,
			]
		);

		// Convert to int.
		$this->assertNotEmpty( $cabin_category_post );
		$cabin_category_post = array_map( 'absint', $cabin_category_post );
		$cabin_category_post = $cabin_category_post[0];

		// Test with cabin post ID.
		track_cabin_post_type_change( $cabin_category_post );

		// Get departures connected to this cabin.
		$departure_posts = get_departures_by_cabin_category_id( $cabin_category_post );
		$this->assertNotEmpty( $departure_posts );

		// Initialize array.
		$expedition_ids2 = [];

		// Get expedition ids from departures of this cabin.
		foreach ( $departure_posts as $departure_post ) {
			$related_expedition_id = absint( get_post_meta( $departure_post, 'related_expedition', true ) );

			// Add to array if not exists.
			if ( ! in_array( $related_expedition_id, $expedition_ids2, true ) ) {
				$expedition_ids2[] = $related_expedition_id;
			}
		}

		// Expedition IDs should not be same.
		$this->assertNotEquals( $expedition_ids, $expedition_ids2 );

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$combined_ids = array_merge( $expedition_ids, $expedition_ids2 );
		$combined_ids = array_unique( $combined_ids );
		$this->assertEqualSets( $combined_ids, $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
	}

	/**
	 * Test track adventure option taxonomy term change.
	 *
	 * @covers \Quark\Ingestor\Urgent\track_adventure_option_taxonomy_change
	 *
	 * @return void
	 */
	public function test_track_adventure_option_taxonomy_change(): void {
		// Test with no args.
		track_adventure_option_taxonomy_change();

		// Option should not be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertEmpty( $option_value );

		// Test with invalid term ID.
		track_adventure_option_taxonomy_change( 0 );

		// Option should not be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertEmpty( $option_value );

		// Create a term.
		$term_id = $this->factory()->term->create();
		$this->assertIsInt( $term_id );

		// Test with non-adventure option term ID.
		track_adventure_option_taxonomy_change( $term_id );

		// Option should not be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertEmpty( $option_value );

		// Create an adventure option term.
		$adventure_option_term_id = $this->factory()->term->create( [ 'taxonomy' => ADVENTURE_OPTION_CATEGORY ] );
		$this->assertIsInt( $adventure_option_term_id );

		// Test with adventure option term ID.
		track_adventure_option_taxonomy_change( $adventure_option_term_id );

		// Option should not be present as no expeditions are connected to this term.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertEmpty( $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );

		// Setup mock response.
		add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

		// Do sync.
		do_sync();

		// Remove filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10 );

		// Get an adventure term.
		$adventure_terms = get_terms(
			[
				'taxonomy'   => ADVENTURE_OPTION_CATEGORY,
				'hide_empty' => false,
				'number'     => 2,
				'fields'     => 'ids',
			]
		);

		// Convert to int.
		$this->assertNotEmpty( $adventure_terms );
		$this->assertIsArray( $adventure_terms );
		$this->assertCount( 2, $adventure_terms );

		// First term.
		$adventure_term1 = $adventure_terms[0];
		$this->assertIsInt( $adventure_term1 );

		// Second term.
		$adventure_term2 = $adventure_terms[1];
		$this->assertIsInt( $adventure_term2 );

		// Test with adventure option term ID.
		track_adventure_option_taxonomy_change( $adventure_term1 );

		// Get departures connected to this term.
		$departure_posts = get_departures_by_adventure_option_term_id( $adventure_term1 );
		$this->assertNotEmpty( $departure_posts );

		// Initialize array.
		$expedition_ids = [];

		// Get expedition IDs.
		foreach ( $departure_posts as $departure_post ) {
			$related_expedition_id = absint( get_post_meta( $departure_post, 'related_expedition', true ) );

			// Add to array if not exists.
			if ( ! in_array( $related_expedition_id, $expedition_ids, true ) ) {
				$expedition_ids[] = $related_expedition_id;
			}
		}

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertEquals( $expedition_ids, $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );

		// Test with adventure option term ID.
		track_adventure_option_taxonomy_change( $adventure_term2 );

		// Get departures connected to this term.
		$departure_posts = get_departures_by_adventure_option_term_id( $adventure_term2 );

		// Initialize array.
		$expedition_ids2 = [];

		// Get expedition IDs.
		foreach ( $departure_posts as $departure_post ) {
			$related_expedition_id = absint( get_post_meta( $departure_post, 'related_expedition', true ) );

			// Add to array if not exists.
			if ( ! in_array( $related_expedition_id, $expedition_ids2, true ) ) {
				$expedition_ids2[] = $related_expedition_id;
			}
		}

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$combined_ids = array_merge( $expedition_ids, $expedition_ids2 );
		$combined_ids = array_unique( $combined_ids );
		$this->assertEquals( $combined_ids, $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );

		// Update title of first term.
		wp_update_term( $adventure_term1, ADVENTURE_OPTION_CATEGORY, [ 'name' => 'Updated Title' ] );

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertEquals( $combined_ids, $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );

		// Update description of first term.
		wp_update_term( $adventure_term1, ADVENTURE_OPTION_CATEGORY, [ 'description' => 'Updated Description' ] );

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertEquals( $combined_ids, $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );

		// Update slug of first term.
		wp_update_term( $adventure_term1, ADVENTURE_OPTION_CATEGORY, [ 'slug' => 'updated-slug' ] );

		// Option should not be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertEmpty( $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );

		// Add image id in meta.
		update_term_meta( $adventure_term1, 'image', $this->factory()->attachment->create_upload_object( TEST_IMAGE_PATH ) );

		// Do action. Ideally this do_action will be done by the WP as part of the term update.
		do_action( 'saved_' . ADVENTURE_OPTION_CATEGORY, $adventure_term1 ); // phpcs:ignore

		// Option should be saved.
		$option_value = get_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
		$this->assertIsArray( $option_value );
		$this->assertEquals( $combined_ids, $option_value );

		// Reset option.
		delete_option( URGENTLY_CHANGED_EXPEDITION_IDS_OPTION );
	}

	/**
	 * Test if urgent push GitHub event is dispatched.
	 *
	 * @covers \Quark\Ingestor\Urgent\dispatch_urgent_push_github_event
	 *
	 * @return void
	 */
	public function test_dispatch_urgent_push_github_event(): void {
		// Setup mock response.
		add_filter( 'pre_http_request', [ $this, 'mock_ingestor_http_request' ], 10, 3 );

		// Test with no args.
		$actual = dispatch_urgent_push_github_event();
		$this->assertFalse( $actual );

		// Test with empty array.
		$actual = dispatch_urgent_push_github_event( [] );
		$this->assertFalse( $actual );

		// Expedition ids.
		$expedition_ids = [ 1, 2, 3 ];

		// Add actions.
		add_action( 'quark_ingestor_dispatch_github_event', [ $this, 'listen_quark_ingestor_dispatch_github_event' ] );

		// Test with expedition ids.
		$actual = dispatch_urgent_push_github_event( $expedition_ids );
		$this->assertFalse( $actual );

		// Do action.
		$this->assertSame( 1, did_action( 'quark_ingestor_dispatch_github_event' ) );

		// Verify data.
		$this->assertEquals(
			[
				'expedition_ids' => $expedition_ids,
				'error'          => 'Github credentials missing',
			],
			$this->dispatch_data
		);

		// Reset dispatch data.
		$this->dispatch_data = [];

		// Set github credentials.
		define( 'QUARK_GITHUB_ACTIONS_TOKEN', self::GH_ACTION_TOKEN );
		define( 'QUARK_GITHUB_API_DISPATCH_URL', self::GH_API_URL );

		// Test with expedition ids.
		$actual = dispatch_urgent_push_github_event( $expedition_ids );
		$this->assertFalse( $actual );

		// Do action.
		$this->assertSame( 2, did_action( 'quark_ingestor_dispatch_github_event' ) );

		// Set ref.
		define( 'QUARK_GITHUB_ACTIONS_REF', self::GH_REF );

		// Get current environment.
		$env = wp_get_environment_type();

		// Set local environment.
		putenv( 'WP_ENVIRONMENT_TYPE=local' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_putenv

		// Test with expedition ids.
		$actual = dispatch_urgent_push_github_event( $expedition_ids );
		$this->assertFalse( $actual );

		// Do action.
		$this->assertSame( 3, did_action( 'quark_ingestor_dispatch_github_event' ) );

		// Set to production environment.
		putenv( 'WP_ENVIRONMENT_TYPE=production' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_putenv

		// Test with expedition ids.
		$actual = dispatch_urgent_push_github_event( $expedition_ids );
		$this->assertTrue( $actual );

		// Do action.
		$this->assertSame( 4, did_action( 'quark_ingestor_dispatch_github_event' ) );

		// Reset environment.
		putenv( 'WP_ENVIRONMENT_TYPE=' . $env ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_putenv

		// Verify data.
		$this->assertEquals(
			[
				'expedition_ids' => $expedition_ids,
				'success'        => 'Github event dispatched',
			],
			$this->dispatch_data
		);

		// Remove filter.
		remove_filter( 'pre_http_request', [ $this, 'mock_ingestor_http_request' ], 10 );
	}

	/**
	 * Listen to the quark_ingestor_dispatch_github_event action.
	 *
	 * @param mixed[] $data Data.
	 *
	 * @return void
	 */
	public function listen_quark_ingestor_dispatch_github_event( array $data = [] ): void {
		// Set data.
		$this->dispatch_data = $data;
	}

	/**
	 * Mock the HTTP request.
	 *
	 * @param mixed[]|false $response    The response.
	 * @param mixed[]       $parsed_args The parsed args.
	 * @param string|null   $url         The URL.
	 *
	 * @return false|array{}|array{
	 *    body: string|false,
	 *    response: array{
	 *      code: int,
	 *      message: string,
	 *    },
	 *    headers: array{},
	 * }
	 */
	public function mock_ingestor_http_request( array|false $response = [], array $parsed_args = [], string $url = null ): false|array {
		// Validate URL.
		if ( empty( $url ) ) {
			return $response;
		}

		// Check if the URL is the one we want to mock.
		if ( ! str_contains( $url, self::GH_API_URL ) ) {
			return $response;
		}

		// Check if the request is a POST request.
		if ( 'POST' !== $parsed_args['method'] ) {
			return $response;
		}

		// Check if the request has the correct headers.
		if ( ! isset( $parsed_args['headers']['Authorization'] ) ) {
			return [
				'body'     => strval( wp_json_encode( [ 'error' => 'Github credentials missing' ] ) ),
				'response' => [
					'code'    => 400,
					'message' => 'Bad Request',
				],
				'headers'  => [],
			];
		}

		// Check if the request has the correct headers.
		if ( 'Bearer ' . self::GH_ACTION_TOKEN !== $parsed_args['headers']['Authorization'] ) {
			return [
				'body'     => strval( wp_json_encode( [ 'error' => 'API Token is invalid' ] ) ),
				'response' => [
					'code'    => 401,
					'message' => 'Unauthorized',
				],
				'headers'  => [],
			];
		}

		// Check body event_type.
		$body = json_decode( $parsed_args['body'], true );

		// Validate empty body.
		if ( empty( $body ) || ! is_array( $body ) ) {
			return [
				'body'     => strval( wp_json_encode( [ 'error' => 'Body is empty' ] ) ),
				'response' => [
					'code'    => 400,
					'message' => 'Bad Request',
				],
				'headers'  => [],
			];
		}

		// Check if the request has the correct body.
		if ( empty( $body['ref'] ) ) {
			return [
				'body'     => strval( wp_json_encode( [ 'error' => 'Ref is empty' ] ) ),
				'response' => [
					'code'    => 400,
					'message' => 'Bad Request',
				],
				'headers'  => [],
			];
		}

		// Return response.
		return [
			'body'     => '',
			'response' => [
				'code'    => 204,
				'message' => 'No Content',
			],
			'headers'  => [],
		];
	}
}
