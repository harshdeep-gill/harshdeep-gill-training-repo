@props( [
	'filters_data' => [],
	'is_compact'   => false,
] )

@php
	if ( empty( $filters_data ) ) {
		return;
	}
@endphp

<div class="expedition-search__sidebar-filters-content-expedition">
	<h5 class="h5 expedition-search__sidebar-filters-content-title">{{ __( 'Expedition', 'qrk' ) }}</h5>
	<x-accordion>
		<x-expedition-search.sidebar-filters.content.expedition.destinations :destinations="$filters_data['destinations']" :is_compact="$is_compact" />
		<x-expedition-search.sidebar-filters.content.expedition.months :months="$filters_data['months']" :is_compact="$is_compact" />
		<x-expedition-search.sidebar-filters.content.expedition.itinerary-lengths :itinerary_lengths="$filters_data['itinerary_lengths']" :is_compact="$is_compact" />
		<x-expedition-search.sidebar-filters.content.expedition.ships :ships="$filters_data['ships']" />
		<x-expedition-search.sidebar-filters.content.expedition.adventure-options :adventure_options="$filters_data['adventure_options']" :is_compact="$is_compact" />
		<x-expedition-search.sidebar-filters.content.expedition.languages :languages="$filters_data['languages']" :is_compact="$is_compact" />
		<x-expedition-search.sidebar-filters.content.expedition.expeditions :expeditions="$filters_data['expeditions']" :is_compact="$is_compact" />
	</x-accordion>
</div>
