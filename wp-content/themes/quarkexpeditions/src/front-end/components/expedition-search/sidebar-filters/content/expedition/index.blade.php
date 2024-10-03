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
		<x-expedition-search.sidebar-filters.content.expedition.months :months="$filters_data['months']" />
		<x-expedition-search.sidebar-filters.content.expedition.ships :ships="$filters_data['ships']" />
		<x-expedition-search.sidebar-filters.content.expedition.adventure-options :adventure_options="$filters_data['adventure_options']" />
		<x-expedition-search.sidebar-filters.content.expedition.languages :languages="$filters_data['languages']" />
		<x-expedition-search.sidebar-filters.content.expedition.expeditions :expeditions="$filters_data['expeditions']" />
	</x-accordion>
</div>
