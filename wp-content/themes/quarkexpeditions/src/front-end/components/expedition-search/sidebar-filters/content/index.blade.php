@props( [
	'filters_data' => [],
] )

@php
	if ( empty( $filters_data ) ) {
		return;
	}
@endphp

<div class="expedition-search__sidebar-filters-content">
	<x-expedition-search.sidebar-filters.content.expedition :filters_data="$filters_data" />
	<x-expedition-search.sidebar-filters.content.cabin :filters_data="$filters_data" />
</div>
