<?php
/**
 * Inclusion Sts test suite.
 *
 * @package quark-inclusion-sets
 */

namespace Quark\InclusionSets\Tests;

use Quark\Tests\Softrip\Softrip_TestCase;

use function Quark\InclusionSets\translate_meta_keys;

/**
 * Class Test_Inclusion_Sets.
 */
class Test_Inclusion_Sets extends Softrip_TestCase {

	/**
	 * Test for translate_meta_keys.
	 *
	 * @covers \Quark\InclusionSets\translate_meta_keys()
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
				'meta_key'      => 'string',
				'icon'          => 'attachment',
				'display_title' => 'string',
				'set_\d+_item'  => 'string',
			],
			translate_meta_keys( $input )
		);
	}
}
