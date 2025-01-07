@props( [
	'title'        => __( 'Filters', 'qrk' ),
	'filters_data' => [],
] )

@php
	if ( empty( $filters_data ) ) {
		return;
	}
@endphp

<quark-expedition-search-sidebar-filters class="expedition-search__sidebar-filters">
	<x-expedition-search.sidebar-filters.header />
	<x-expedition-search.sidebar-filters.inputs-container>
		<x-expedition-search.sidebar-filters.content :filters_data="$filters_data" />
	</x-expedition-search.sidebar-filters.inputs-container>
</quark-expedition-search-sidebar-filters>

<x-expedition-search.sidebar-filters.sticky-filters>
	<x-expedition-search.sidebar-filters.content :filters_data="$filters_data" :is_compact="true" />
</x-expedition-search.sidebar-filters.sticky-filters>
