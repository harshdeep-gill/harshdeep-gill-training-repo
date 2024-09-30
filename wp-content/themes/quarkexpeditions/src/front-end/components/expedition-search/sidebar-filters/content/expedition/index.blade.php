@props( [
	'filters_data' => [],
] )

@php
	if ( empty( $filters_data ) ) {
		return;
	}
@endphp

<div class="expedition-search__sidebar-filters-content-expedition">
	<h5 class="h5 expedition-search__sidebar-filters-content-title">{{ __( 'Expedition', 'qrk' ) }}</h5>
	<x-accordion>
		<x-expedition-search.sidebar-filters.content.expedition.destinations :destinations="$filters_data['destinations']" />
	</x-accordion>
</div>
