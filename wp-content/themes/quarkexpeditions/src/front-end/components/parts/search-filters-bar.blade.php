@props( [
	'antarctic_image_id' => 0,
	'arctic_image_id'    => 0,
	'antarctic_cta'  => '',
	'arctic_cta'     => '',
] )

@php
	// Get filters data.
	$search_filter_data = Quark\Search\Filters\get_destination_and_month_filter_options();

	// Get destinations.
	$destinations = $search_filter_data['destinations'] ?? [];

	// Get departure months.
	$departure_months = $search_filter_data['months'] ?? [];

	// Get filters API URL.
	$filters_api_url = quark_get_template_data( 'filters_api_url' );

	// Get search page url.
	$search_page_url = quark_get_template_data( 'search_page_url' );

	// Image Ids.
	$image_ids = [];

	if ( ! empty( $antarctic_image_id ) ) {
		$image_ids['antarctic'] = $antarctic_image_id;
	}

	if ( ! empty( $arctic_image_id ) ) {
		$image_ids['arctic'] = $arctic_image_id;
	}

	// CTA URLs.
	$cta_urls = [];

	if ( ! empty( $antarctic_cta ) ) {
		$cta_urls['antarctic'] = $antarctic_cta;
	}

	if ( ! empty( $arctic_cta ) ) {
		$cta_urls['arctic'] = $arctic_cta;
	}
@endphp

<x-search-filters-bar
	:destinations="$destinations"
	:available_months="$departure_months"
	:filters_api_url="$filters_api_url"
	:search_page_url="$search_page_url"
	:image_ids="$image_ids"
	:cta_urls="$cta_urls"
/>
