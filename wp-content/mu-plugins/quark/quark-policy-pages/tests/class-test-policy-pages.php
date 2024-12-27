<?php
/**
 * Policy Pages test suite.
 *
 * @package quark-policy-pages
 */

namespace Quark\PolicyPages;

use WP_UnitTestCase;

/**
 * Class Test_Policy_Pages.
 */
class Test_Policy_Pages extends WP_UnitTestCase {

	/**
	 * Test for translate_meta_keys.
	 *
	 * @covers \Quark\PolicyPages\translate_meta_keys()
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
				'meta_key'                 => 'string',
				'icon'                     => 'attachment',
				'alternate_title'          => 'string',
				'marketing_option_icon'    => 'attachment',
				'marketing_option_summary' => 'string',
			],
			translate_meta_keys( $input )
		);
	}
}
