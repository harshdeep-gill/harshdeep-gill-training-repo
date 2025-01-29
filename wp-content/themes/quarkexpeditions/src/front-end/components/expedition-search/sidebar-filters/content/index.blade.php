@props( [
	'filters_data' => [],
	'is_compact'   => false,
] )

@php
	if ( empty( $filters_data ) ) {
		return;
	}
@endphp

<div class="expedition-search__sidebar-filters-content">
	<x-expedition-search.sidebar-filters.content.expedition :filters_data="$filters_data" :is_compact="$is_compact" />
	<x-expedition-search.sidebar-filters.content.cabin :filters_data="$filters_data" :is_compact="$is_compact" />
</div>
