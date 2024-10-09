@php
	// Get filters data.
	// TODO: Remove once data is passed in from block.
	$search_filter_data = Quark\Search\Filters\get_destination_and_month_filter_options();

	// Get destinations.
	$destinations = $search_filter_data['destinations'] ?? [];

	// Get departure months.
	$departure_months = $search_filter_data['months'] ?? [];

	// Get filters API URL.
	$filters_api_url = quark_get_template_data( 'filters_api_url' );

	// Get search page url.
	$search_page_url = home_url( '/expedition-search' ); // TODO: Replace with actual value from field.
@endphp
<x-search-filters-bar
	:destinations="$destinations"
	:available_months="$departure_months"
	:filters_api_url="$filters_api_url"
	:search_page_url="$search_page_url"
/>
