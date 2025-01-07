@props( [
	'filter_data' => [],
] )

@php
	if ( empty( $filter_data ) || ! is_array( $filter_data ) ) {
		return;
	}

	var_dump( $filter_data );
@endphp

<x-expedition-search.sidebar-filters :filters_data="$filters_data" />
