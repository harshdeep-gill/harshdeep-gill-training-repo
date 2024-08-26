<?php
/**
 * Test suite for manual sync.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Tests\ManualSync;

use Exception;
use WP_UnitTestCase;

use function Quark\Softrip\ManualSync\get_sync_admin_url;
use function Quark\Softrip\ManualSync\manual_sync_handle_redirect;

/**
 * Class Test_Manual_Sync
 */
class Test_Manual_Sync extends WP_UnitTestCase {
	/**
	 * Get sync admin url.
	 *
	 * @covers \Quark\Softrip\get_sync_admin_url()
	 *
	 * @return void
	 */
	public function test_get_sync_admin_url(): void {
		// Test with empty arg.
		$result = get_sync_admin_url();
		$this->assertEmpty( $result );

		// Test with valid post id.
		$post_id = 123;
		$result  = get_sync_admin_url( $post_id );

		// Unescape the URL.
		$result = html_entity_decode( $result );

		// Assert the URL.
		$expected = 'http://test.quarkexpeditions.com/wp-admin/admin.php?action=sync&post_id=' . $post_id . '&sync=';
		$this->assertStringContainsString( $expected, $result );
	}

	/**
	 * Test handle redirect to post page.
	 *
	 * @covers \Quark\Softrip\manual_sync_handle_redirect()
	 *
	 * @return void
	 */
	public function test_manual_sync_handle_redirect(): void {
		// Halt redirect to admin home.
		add_filter( 'wp_redirect', [ $this, 'wp_redirect_halt_redirect' ], 10, 2 );

		// Exception data.
		$e_data = [];

		// Test case 1: Test with empty arg.
		try {
			// Test with empty arg.
			manual_sync_handle_redirect();
		} catch ( Exception $e ) {
			$e_data = (array) json_decode( $e->getMessage(), true );
		}

		// Should redirect to admin home.
		$this->assertNotEmpty( $e_data );
		$this->assertArrayHasKey( 'location', $e_data );
		$this->assertNotEmpty( $e_data['location'] );
		$this->assertSame( admin_url(), $e_data['location'] );

		// Valid post id.
		$post_id       = 123;
		$e_data        = [];
		$redirect_args = [ 'success' => 'true' ];

		// Test case 2: Test with valid post id.
		try {
			// Test with valid post id.
			manual_sync_handle_redirect( $post_id, $redirect_args );
		} catch ( Exception $e ) {
			$e_data = (array) json_decode( $e->getMessage(), true );
		}

		// Should redirect to post page.
		$this->assertNotEmpty( $e_data );
		$this->assertArrayHasKey( 'location', $e_data );
		$this->assertNotEmpty( $e_data['location'] );
		$expected_url = add_query_arg(
			array_merge(
				[
					'post'   => $post_id,
					'action' => 'edit',
				],
				$redirect_args
			),
			admin_url( 'post.php' )
		);
		$this->assertSame( $expected_url, $e_data['location'] );

		// Removal.
		remove_filter( 'wp_redirect', [ $this, 'wp_redirect_halt_redirect' ], 10 );
	}

	/**
	 * Halt redirect to admin home.
	 *
	 * @param string $location URL to redirect.
	 * @param int    $status  Status code.
	 *
	 * @throws Exception Throws exception with location and status.
	 *
	 * @return void
	 */
	public function wp_redirect_halt_redirect( string $location = '', int $status = 302 ): void {
		// Throw exception with location and status.
		throw new Exception(
			strval( // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
				wp_json_encode(
					[
						'location' => $location,
						'status'   => $status,
					]
				)
			)
		);
	}
}
