<?php
/**
 * Softrip test suite.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip;

use WP_Error;
use WP_UnitTestCase;

class Test_Softrip extends WP_UnitTestCase {
    /**
     * Test case for requesting departure from middleware.
     *
     * @covers \Quark\Softrip\request_departures()
     *
     * @return void
     */
    public function test_request_departures(): void {
        // Setup mock response.
        add_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request', 10, 3 );

        // Test case 1: No argument passed.
        $result = request_departures();
        $this->assertTrue( $result instanceof WP_Error );
        $this->assertSame( 'qrk_softrip_departures_limit', $result->get_error_code() );
        $this->assertSame( 'The maximum number of codes allowed is 5', $result->get_error_message() );

        // Test case 2: Empty array passed.
        $result = request_departures( [] );
        $this->assertTrue( $result instanceof WP_Error );
        $this->assertSame( 'qrk_softrip_departures_limit', $result->get_error_code() );
        $this->assertSame( 'The maximum number of codes allowed is 5', $result->get_error_message() );

        // Test case 3: Test code array with more than 5 elements.
        $test_codes = [
            'ABC-123',
            'DEF-456',
            'GHI-789',
            'JKL-012',
            'MNO-345',
            'PQR-678',
        ];
        $result = request_departures( $test_codes );
        $this->assertTrue( $result instanceof WP_Error );
        $this->assertSame( 'qrk_softrip_departures_limit', $result->get_error_code() );
        $this->assertSame( 'The maximum number of codes allowed is 5', $result->get_error_message() );

        // Test case 4: Test code array with one element.
        $test_codes = [ 'ABC-123' ];
        $result = request_departures( $test_codes );
        $this->assertIsArray( $result );
        $this->assertArrayHasKey( 'ABC-123', $result );

        // Test case 5: Test code array with five elements with only a few valid.
        $test_codes = [
            'ABC-123',
            'DEF-456',
            'GHI-789',
            'JKL-012',
            'MNO-345',
        ];
        $result = request_departures( $test_codes );
        $this->assertIsArray( $result );
        $this->assertArrayHasKey( 'ABC-123', $result );
        $this->assertArrayNotHasKey( 'DEF-456', $result ); // Invalid code.
        $this->assertArrayNotHasKey( 'GHI-789', $result ); // Invalid code.
        $this->assertArrayHasKey( 'JKL-012', $result );
        $this->assertArrayNotHasKey( 'MNO-345', $result ); // Invalid code.

        // Cleanup.
        remove_filter( 'pre_http_request', 'Quark\Tests\Softrip\mock_softrip_http_request' );
    }
}
