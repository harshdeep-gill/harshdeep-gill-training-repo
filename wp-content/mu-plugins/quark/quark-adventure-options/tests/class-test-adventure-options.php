<?php
/**
 * Test suite for Quark Adventure Options.
 *
 * @package quark-adventure-options
 */

namespace Quark\AdventureOptions\Tests;

use WP_Post;
use WP_UnitTestCase;

use function Quark\AdventureOptions\get_breadcrumbs_ancestors;
use function Quark\AdventureOptions\translate_term_meta_keys;

/**
 * Class Test_Adventure_Options.
 */
class Test_Adventure_Options extends WP_UnitTestCase {

	/**
	 * Test get breadcrumbs.
	 *
	 * @covers \Quark\AdventureOptions\get_breadcrumbs_ancestors()
	 *
	 * @return void
	 */
	public function test_get_breadcrumbs_ancestors(): void {
		// Test with no ancestors.
		$this->assertEmpty( get_breadcrumbs_ancestors() );

		// Create a page.
		$page = $this->factory()->post->create_and_get(
			[
				'post_title' => 'Test Page',
				'post_type'  => 'page',
			]
		);
		$this->assertTrue( $page instanceof WP_Post );

		// Set as archive page.
		update_option( 'options_adventure_options_page', $page->ID );

		// Assert the breadcrumbs.
		$this->assertEquals(
			[
				[
					'title' => 'Test Page',
					'url'   => get_permalink( $page ),
				],
			],
			get_breadcrumbs_ancestors()
		);
	}

	/**
	 * Test for translate_term_meta_keys.
	 *
	 * @covers \Quark\AdventureOptions\translate_term_meta_keys()
	 *
	 * @return void
	 */
	public function test_translate_term_meta_keys(): void {
		// Input data.
		$input = [
			'meta_key' => 'string',
			'icon'     => 'attachment',
		];

		// Assert data.
		$this->assertEquals(
			[
				'meta_key' => 'string',
				'image'    => 'attachment',
				'icon'     => 'attachment',
			],
			translate_term_meta_keys( $input )
		);
	}
}
