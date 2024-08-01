<?php
/**
 * Softrip test occupancy.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_UnitTestCase;
use WP_Post;
use WP_Error;

use function Quark\Tests\tear_down_softrip_db;

use const Quark\Departures\POST_TYPE as DEPARTURE_POST_TYPE;

/**
 * Class Test_Occupancy.
 */
class Test_Occupancy extends WP_UnitTestCase {
	/**
	 * Tear down after class.
	 *
	 * @return void
	 */
	public static function tear_down_after_class(): void {
		// Run parent.
		parent::tear_down_after_class();
		tear_down_softrip_db();
	}

	/**
	 * Setup for tests.
	 *
	 * @return void
	 */
	public function set_up(): void {
		// Run parent.
		parent::set_up();

		// Mock the response for the POST request.
		add_filter( 'pre_http_request', 'Quark\Tests\mock_softrip_http_request', 10, 3 );
	}

	/**
	 * Tear down after tests.
	 *
	 * @return void
	 */
	public function tear_down(): void {
		// Run parent.
		parent::tear_down();

		// Remove the filter.
		remove_filter( 'pre_http_request', 'Quark\Tests\mock_softrip_http_request' );
	}

	/**
	 * Get a post to test with.
	 *
	 * @return WP_Post|WP_Error
	 */
	public function get_post(): WP_Post|WP_Error {
		// Create and return a post.
		return $this->factory()->post->create_and_get(
			[
				'post_title'   => 'Test Post',
				'post_content' => 'Post content',
				'post_status'  => 'publish',
				'post_type'    => DEPARTURE_POST_TYPE,
				'meta_input'   => [
					'test_meta' => true,
				],
			]
		);
	}

	/**
	 * Make a cabin object.
	 *
	 * @return Cabin
	 */
	public function get_cabin(): Cabin {
		// Create Cabin.
		$cabin          = new Cabin();
		$departure_post = $this->get_post();

		// Add a departure post if found.
		if ( ! $departure_post instanceof WP_Error ) {
			$departure = new Departure();
			$departure->load( $departure_post->ID );
			$cabin->set_departure( $departure );
		}

		// Return new object.
		return $cabin;
	}

	/**
	 * Test occupancies.
	 *
	 * @covers \Quark\Softrip\Cabin::get_occupancies()
	 *
	 * @return void
	 */
	public function test_occupancies(): void {
		// Get a cabin.
		$cabin = $this->get_cabin();

		// Get Data.
		$data = $this->get_test_data();

		// Set the data.
		$cabin->set( $data );

		// Get occupancies.
		$occupancies = $cabin->get_occupancies();
		$first       = array_shift( $occupancies );
		$second      = array_shift( $occupancies );

		// Test if the occupancies exist.
		if ( ! empty( $first ) ) {
			$this->assertEquals( 'ARC-ISLN-15D2024:2024-08-25:OAD-TPL:AAA', $first->get_entry_data( 'title' ) );
		}

		// Test if the occupancies exist.
		if ( ! empty( $second ) ) {
			$this->assertEquals( 'ARC-ISLN-15D2024:2024-08-25:OAD-TPL:SAAA', $second->get_entry_data( 'title' ) );
		}
	}

	/**
	 * Get test data.
	 *
	 * @return mixed[]
	 */
	public function get_test_data(): array {
		// Define a json object.
		$json = '{
	        "id": "ARC-ISLN-15D2024:2024-08-25:OAD-TPL",
	        "code": "OAD-TPL",
	        "name": "Triple",
	        "departureId": "ARC-ISLN-15D2024:2024-08-25",
	        "occupancies": [
	            {
	                "id": "ARC-ISLN-15D2024:2024-08-25:OAD-TPL:AAA",
	                "name": "ARC-ISLN-15D2024:2024-08-25:OAD-TPL:AAA",
	                "mask": "AAA",
	                "availabilityStatus": "C",
	                "availabilityDescription": "Unavailable",
	                "spacesAvailable": 0,
	                "seq": "100",
	                "prices": {
	                    "USD": {
	                        "currencyCode": "USD",
	                        "pricePerPerson": 10795,
	                        "promos": {
	                            "30PROMO": {
	                                "promoPricePerPerson": 7557
	                            }
	                        }
	                    },
	                    "AUD": {
	                        "currencyCode": "AUD",
	                        "pricePerPerson": 16700,
	                        "promos": {
	                            "30PROMO": {
	                                "promoPricePerPerson": 11690
	                            }
	                        }
	                    },
	                    "CAD": {
	                        "currencyCode": "CAD",
	                        "pricePerPerson": 14300,
	                        "promos": {
	                            "30PROMO": {
	                                "promoPricePerPerson": 10010
	                            }
	                        }
	                    },
	                    "EUR": {
	                        "currencyCode": "EUR",
	                        "pricePerPerson": 10000,
	                        "promos": {
	                            "30PROMO": {
	                                "promoPricePerPerson": 7000
	                            }
	                        }
	                    },
	                    "GBP": {
	                        "currencyCode": "GBP",
	                        "pricePerPerson": 8800,
	                        "promos": {
	                            "30PROMO": {
	                                "promoPricePerPerson": 6160
	                            }
	                        }
	                    }
	                }
	            },
	            {
	                "id": "ARC-ISLN-15D2024:2024-08-25:OAD-TPL:SAAA",
	                "name": "ARC-ISLN-15D2024:2024-08-25:OAD-TPL:SAAA",
	                "mask": "SAAA",
	                "availabilityStatus": "C",
	                "availabilityDescription": "Unavailable",
	                "spacesAvailable": 0,
	                "seq": "100",
	                "prices": {
	                    "USD": {
	                        "currencyCode": "USD",
	                        "pricePerPerson": 10795,
	                        "promos": {
	                            "30PROMO": {
	                                "promoPricePerPerson": 7557
	                            }
	                        }
	                    },
	                    "AUD": {
	                        "currencyCode": "AUD",
	                        "pricePerPerson": 16700,
	                        "promos": {
	                            "30PROMO": {
	                                "promoPricePerPerson": 11690
	                            }
	                        }
	                    },
	                    "CAD": {
	                        "currencyCode": "CAD",
	                        "pricePerPerson": 14300,
	                        "promos": {
	                            "30PROMO": {
	                                "promoPricePerPerson": 10010
	                            }
	                        }
	                    },
	                    "EUR": {
	                        "currencyCode": "EUR",
	                        "pricePerPerson": 10000,
	                        "promos": {
	                            "30PROMO": {
	                                "promoPricePerPerson": 7000
	                            }
	                        }
	                    },
	                    "GBP": {
	                        "currencyCode": "GBP",
	                        "pricePerPerson": 8800,
	                        "promos": {
	                            "30PROMO": {
	                                "promoPricePerPerson": 6160
	                            }
	                        }
	                    }
	                }
	            }
	        ]
        }';

		// Return the parsed array.
		return json_decode( $json, true );
	}
}
