@props( [
	'filters_data' => [],
	'is_compact'   => false,
] )

@php
	if ( empty( $filters_data ) || ! is_array( $filters_data ) ) {
		return;
	}
@endphp

<x-expedition-search.sidebar-filters.content :filters_data="$filters_data" :is_compact="$is_compact" />
