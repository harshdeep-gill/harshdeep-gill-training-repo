@props( [
	'filter_data' => []
] )

@php
	if ( empty( $filter_data ) || ! is_array( $filter_data ) ) {
		return;
	}
@endphp

<x-dates-rates.filters.accordion :filter_data="$filter_data" />
