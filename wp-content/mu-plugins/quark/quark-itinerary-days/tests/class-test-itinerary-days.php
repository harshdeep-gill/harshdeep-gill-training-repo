<?php
/**
 * Itineray Days test suite.
 *
 * @package quark-inclusion-sets
 */

namespace Quark\ItineraryDays;

use Quark\Tests\Softrip\Softrip_TestCase;

/**
 * Class Test_Itinerary_Days.
 */
class Test_Itinerary_Days extends Softrip_TestCase {

	/**
	 * Test for translate_meta_keys.
	 *
	 * @covers \Quark\ItineraryDays\translate_meta_keys()
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
				'meta_key'  => 'string',
				'icon'      => 'attachment',
				'day_title' => 'string',
				'port'      => 'post',
				'location'  => 'string',
			],
			translate_meta_keys( $input )
		);
	}
}
