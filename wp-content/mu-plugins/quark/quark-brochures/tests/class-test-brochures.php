<?php
/**
 * Brochurese test suite.
 *
 * @package quark-brochures
 */

namespace Quark\Brochures\Tests;

use WP_UnitTestCase;
use WP_Post;

use function Quark\Brochures\translate_meta_keys;

use const Quark\Brochures\POST_TYPE;

/**
 * Class Test_brochures.
 */
class Test_Brochures extends WP_UnitTestCase {

	/**
	 * Test bootstrap.
	 *
	 * @covers \Quark\Brochures\bootstrap()
	 *
	 * @return void
	 */
	public function test_bootstrap(): void {
		// Test if post type hook is registered.
		$this->assertEquals( 10, has_filter( 'init', 'Quark\Brochures\register_brochure_post_type' ) );
		$this->assertEquals( 10, has_filter( 'qrk_translation_meta_keys', 'Quark\Brochures\translate_meta_keys' ) );
	}

	/**
	 * Make sure post type is registered.
	 *
	 * @covers \Quark\Brochures\register_brochure_post_type()
	 *
	 * @return void
	 */
	public function test_register_brochure_post_type(): void {
		// Test if post type is actually registered.
		$this->assertTrue( post_type_exists( POST_TYPE ) );
	}

	/**
	 * Test getting a base.
	 *
	 * @covers \Quark\Brochures\get()
	 *
	 * @return void
	 */
	public function test_get(): void {
		// Create post.
		$post_1 = $this->factory()->post->create_and_get(
			[
				'post_type'   => POST_TYPE,
				'post_title'  => 'Test Post',
				'post_status' => 'publish',
				'meta_input'  => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
			]
		);

		// Make sure post is created.
		$this->assertTrue( $post_1 instanceof WP_Post );

		// Test getting post.
		$this->assertEquals(
			[
				'post'           => $post_1,
				'post_thumbnail' => 0,
				'post_meta'      => [
					'meta_1' => 'value_1',
					'meta_2' => 'value_2',
				],
			],
			\Quark\Brochures\get( $post_1->ID )
		);
	}

	/**
	 * Test for translate_meta_keys.
	 *
	 * @covers \Quark\Brochures\translate_meta_keys()
	 *
	 * @return void
	 */
	public function test_translate_meta_keys(): void {
		// Input data.
		$input = [
			'meta_key' => 'string',
			'icon'     => 'attachment',
		];

		// Assert data.
		$this->assertEquals(
			[
				'meta_key'     => 'string',
				'icon'         => 'attachment',
				'brochure_pdf' => 'attachment',
			],
			translate_meta_keys( $input )
		);
	}
}
