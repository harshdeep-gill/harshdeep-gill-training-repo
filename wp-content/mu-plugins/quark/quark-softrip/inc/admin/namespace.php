<?php
/**
 * Namespace for the Softrip database pages.
 *
 * @package quark-softrip
 */

namespace Quark\Softrip\Admin;

use function Quark\Softrip\get_custom_db_table_mapping;

const ADMIN_PAGE_SLUG = 'softrip-database-tables';

/**
 * Bootstrap the admin pages.
 *
 * @return void
 */
function bootstrap(): void {
	// Register admin pages.
	add_action( 'admin_menu', __NAMESPACE__ . '\\register_custom_table_listing_pages' );
}

/**
 * Register admin pages.
 *
 * @return void
 */
function register_custom_table_listing_pages(): void {
	// Add a new top-level menu.
	add_menu_page(
		__( 'Softrip Custom Table', 'quark' ),
		'Softrip Tables',
		'manage_options',
		ADMIN_PAGE_SLUG,
		__NAMESPACE__ . '\\render_softrip_table_listing_pages',
		'dashicons-database-view',
	);

	// Get custom table lists.
	$tables = get_custom_db_table_mapping();

	// Add submenus.
	foreach ( $tables as $table_name => $sql ) {
		// Page title.
		$title = ucwords( str_replace( [ 'qrk', '_' ], [ '', ' ' ], $table_name ) );

		// Add submenu.
		add_submenu_page(
			ADMIN_PAGE_SLUG,
			$title,
			$title,
			'manage_options',
			$table_name,
			__NAMESPACE__ . '\\render_custom_table_records'
		);
	}
}

/**
 * Render custom admin page content.
 *
 * @return void
 */
function render_softrip_table_listing_pages(): void {
	// Create a header in the default WordPress 'wrap' container.
	echo '<div class="wrap"><h2>Softrip Custom Tables</h2></div>';
	echo '<ul>';

	// Get custom table lists.
	$tables = get_custom_db_table_mapping();

	// Add submenus links.
	foreach ( $tables as $table_name => $sql ) {
		// Page title.
		$title = ucwords( str_replace( [ '_' ], [ ' ' ], $table_name ) );

		// Add submenu link.
		printf(
			'<li><a href="%1$s">%2$s</a></li>',
			esc_url( admin_url( 'admin.php?page=' . $table_name ) ),
			esc_html( $title )
		);
	}

	// End list.
	echo '</ul>';
}

/**
 * Render custom table.
 *
 * @return void
 */
function render_custom_table_records(): void {
	// Get the table name from slug.
	global $plugin_page;

	// Get the tables name.
	$custom_tables = get_custom_db_table_mapping();

	// Verify the table name.
	if ( empty( $plugin_page ) || ! array_key_exists( $plugin_page, $custom_tables ) ) {
		return;
	}

	// Get the fields.
	$table_name = $plugin_page;
	$fields     = get_custom_table_fields( $table_name );

	// Verify fields.
	if ( empty( $fields ) ) {
		return;
	}

	// Get global DB object.
	global $wpdb;

	// Create a header in the default WordPress 'wrap' container.
	printf( '<div class="wrap"><h2>%1$s (%2$s)</h2></div>', esc_html( get_admin_page_title() ), esc_html( $table_name ) );

	// Add back to custom table list link.
	printf(
		'<a href="%1$s" class="page-title-action">Back to Tables</a>',
		esc_url( admin_url( 'admin.php?page=' . ADMIN_PAGE_SLUG ) )
	);

	// Get pagination page number.
	$page     = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1; // phpcs:ignore
	$per_page = 100;
	$offset   = ( $page - 1 ) * $per_page;

	// Load the table records.
	$table_records = $wpdb->get_results(
		$wpdb->prepare(
			'
			SELECT
				*
			FROM
				%i
			LIMIT %d, %d
			',
			[
				$table_name,
				$offset,
				$per_page,
			]
		),
		ARRAY_A
	);

	// Check if there are any records.
	if ( empty( $table_records ) || ! is_array( $table_records ) ) {
		echo '<p>No records found.</p>';

		// bail out.
		return;
	}

	// Create a table to display the records.
	echo '<table class="wp-list-table widefat fixed striped"><thead>';
	echo '<tr>';

	// Display the table headers.
	foreach ( $fields as $field => $label ) {
		echo '<th>' . esc_html( $label ) . '</th>';
	}

	// Close the header row.
	echo '</tr></thead>';

	// Start the table body.
	echo '<tbody>';

	// Loop through the records.
	foreach ( $table_records as $record ) {
		$record = wp_parse_args( $record, array_fill_keys( array_keys( $fields ), '' ) );

		// Start a new row.
		echo '<tr>';

		// Display the record.
		foreach ( $fields as $field => $label ) {
			echo '<td>' . esc_html( $record[ $field ] ) . '</td>';
		}

		// Close the row.
		echo '</tr>';
	}

	// Close the table.
	echo '</tbody></table>';

	// Pagination.
	$total        = $wpdb->get_var(
		$wpdb->prepare(
			'
			SELECT
				COUNT(*)
			FROM
				%i
			',
			[
				$table_name,
			]
		)
	);
	$num_of_pages = ceil( $total / $per_page );

	// Pagination links.
	$page_links = paginate_links(
		[
			'base'      => add_query_arg( 'paged', '%#%' ),
			'format'    => '',
			'prev_text' => __( '&laquo;' ),
			'next_text' => __( '&raquo;' ),
			'total'     => $num_of_pages,
			'current'   => $page,
		]
	);

	// Display the pagination links.
	if ( $page_links ) {
		echo '<div class="tablenav bottom">';
		echo '<span class="displaying-num">' . esc_html( $total ) . ' Records</span>';
		echo '<span class="pagination-links">' . wp_kses_post( strval( $page_links ) ) . '</span>';
		echo '</div>';
	}
}

/**
 * Get custom table fields.
 *
 * @param string $table_name The table name.
 *
 * @return array{}|array<string, string> The fields.
 */
function get_custom_table_fields( string $table_name = '' ): array {
	// Verify not empty.
	if ( empty( $table_name ) ) {
		return [];
	}

	// Define the fields to display.
	return match ( $table_name ) {
		'qrk_adventure_options' => [
			'id'                       => 'ID',
			'softrip_option_id'        => 'Softrip Option ID',
			'departure_post_id'        => 'Departure Post Id',
			'softrip_package_code'     => 'Softrip Package Code',
			'service_ids'              => 'Service Ids',
			'spaces_available'         => 'Spaces Available',
			'adventure_option_term_id' => 'Adventure Option Term Id',
			'price_per_person_usd'     => 'Price Per Person - USD',
			'price_per_person_cad'     => 'Price Per Person - CAD',
			'price_per_person_aud'     => 'Price Per Person - AUD',
			'price_per_person_gbp'     => 'Price Per Person - GBP',
			'price_per_person_eur'     => 'Price Per Person - EUR',
		],
		'qrk_promotions' => [
			'id'             => 'ID',
			'code'           => 'Code',
			'start_date'     => 'Start Date',
			'end_date'       => 'End Date',
			'description'    => 'Description',
			'discount_type'  => 'Discount Type',
			'discount_value' => 'Discount Value',
			'is_pif'         => 'Is PIF',
		],
		'qrk_occupancies' => [
			'id'                       => 'ID',
			'softrip_id'               => 'Softrip ID',
			'softrip_name'             => 'Sotrip Name',
			'mask'                     => 'Mask',
			'departure_post_id'        => 'Departure Post ID',
			'cabin_category_post_id'   => 'Cabin Category Post ID',
			'spaces_available'         => 'Spaces Available',
			'availability_description' => 'Availability Description',
			'availability_status'      => 'Availability Status',
			'price_per_person_usd'     => 'Price Per Person - USD',
			'price_per_person_cad'     => 'Price Per Person - CAD',
			'price_per_person_aud'     => 'Price Per Person - AUD',
			'price_per_person_gbp'     => 'Price Per Person - GBP',
			'price_per_person_eur'     => 'Price Per Person - EUR',
		],
		'qrk_occupancy_promotions' => [
			'id'                   => 'ID',
			'occupancy_id'         => 'Occupancy ID',
			'promotion_id'         => 'Promotion ID',
			'price_per_person_usd' => 'Price Per Person - USD',
			'price_per_person_cad' => 'Price Per Person - CAD',
			'price_per_person_aud' => 'Price Per Person - AUD',
			'price_per_person_gbp' => 'Price Per Person - GBP',
			'price_per_person_eur' => 'Price Per Person - EUR',
		],
		default => [],
	};
}
