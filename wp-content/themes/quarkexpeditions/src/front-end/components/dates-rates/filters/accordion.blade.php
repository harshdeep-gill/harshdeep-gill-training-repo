@props( [
	'filter_data' => [],
] )

@php
	if ( empty( $filter_data ) || ! is_array( $filter_data ) ) {
		return;
	}
@endphp

<x-accordion>

	@if ( ! empty( $filter_data['seasons'] ) )
		<x-accordion.item id="filters-accordion-seasons">
			<x-accordion.item-handle :title="__( 'Region & Season', 'qrk' )" />
			<x-accordion.item-content>
				<x-dates-rates.filters.seasons :seasons="$filter_data['seasons']" />
			</x-accordion.item-content>
		</x-accordion.item>
	@endif

	@if ( ! empty( $filter_data['expeditions'] ) )
		<x-accordion.item id="filters-accordion-expeditions">
			<x-accordion.item-handle :title="__( 'Expedition', 'qrk' )" />
			<x-accordion.item-content>
				<x-dates-rates.filters.expeditions :expeditions="$filter_data['expeditions']" />
			</x-accordion.item-content>
		</x-accordion.item>
	@endif

	@if ( ! empty( $filter_data['adventure_options'] ) )
		<x-accordion.item id="filters-accordion-adventure-options">
			<x-accordion.item-handle :title="__( 'Adventure Options (with availability)', 'qrk' )" />
			<x-accordion.item-content>
				<x-dates-rates.filters.adventure-options :adventure_options="$filter_data['adventure_options']" />
			</x-accordion.item-content>
		</x-accordion.item>
	@endif

	@if ( ! empty( $filter_data['months'] ) )
		<x-accordion.item id="filters-accordion-months">
			<x-accordion.item-handle :title="__( 'Departure Month', 'qrk' )" />
			<x-accordion.item-content>
				<x-dates-rates.filters.departure-months :months="$filter_data['months']" />
			</x-accordion.item-content>
		</x-accordion.item>
	@endif

	@if ( ! empty( $filter_data['durations'] ) )
		<x-accordion.item id="filters-accordion-durations">
			<x-accordion.item-handle :title="__( 'Duration of Voyage (days)', 'qrk' )" />
			<x-accordion.item-content>
				<x-dates-rates.filters.durations :durations="$filter_data['durations']" />
			</x-accordion.item-content>
		</x-accordion.item>
	@endif

	@if ( ! empty( $filter_data['ships'] ) )
		<x-accordion.item id="filters-accordion-ships">
			<x-accordion.item-handle :title="__( 'Ship', 'qrk' )" />
			<x-accordion.item-content>
				<x-dates-rates.filters.ships :ships="$filter_data['ships']" />
			</x-accordion.item-content>
		</x-accordion.item>
	@endif
</x-accordion>
