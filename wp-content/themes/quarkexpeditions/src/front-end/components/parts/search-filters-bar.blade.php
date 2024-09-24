@php
	// Get filters data.
	// TODO: Remove once data is passed in from block.
	$search_filter_data = Quark\Search\Filters\get_destination_and_month_filter_options();

	// Get destinations.
	$destinations = $search_filter_data['destinations'] ?? [];

	// Get departure months.
	$departure_months = $search_filter_data['months'] ?? [];
@endphp
<x-search-filters-bar :destinations="$destinations" :available_months="$departure_months" />