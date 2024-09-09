@props( [
	'filter_data' => []
] )

@php
	if ( empty( $filter_data ) || ! is_array( $filter_data ) ) {
		return;
	}
@endphp

<div class="dates-rates__filters-container">
	<h2 class="dates-rates__filters-heading">{{ __( 'Filters', 'qrk' ) }}</h2>
	<div class="dates-rates__filters">
		<x-dates-rates.filters.chips>
			<x-dates-rates.filters.chip drawer_id="dates-rates-filters" title="Region & Season" accordion_id="filters-accordion-seasons" />
			<x-dates-rates.filters.chip drawer_id="dates-rates-filters" title="Expedition" accordion_id="filters-accordion-expeditions" />
			<x-dates-rates.filters.chip drawer_id="dates-rates-filters" title="Adventure Options" accordion_id="filters-accordion-adventure-options" />
			<x-dates-rates.filters.chip drawer_id="dates-rates-filters" title="Departure Month" accordion_id="filters-accordion-months" />
			<x-dates-rates.filters.chip drawer_id="dates-rates-filters" title="Duration" accordion_id="filters-accordion-durations" />
			<x-dates-rates.filters.chip drawer_id="dates-rates-filters" title="Ship" accordion_id="filters-accordion-ships" />
		</x-dates-rates.filters.chips>

		<x-dates-rates.filters.currency-dropdown />
	</div>

	<x-dates-rates.filters.selected />

	<x-dates-rates.filters.sticky>
		<x-dates-rates.filters.sticky-filter drawer_id="dates-rates-filters" accordion_id="filters-accordion-seasons" />
		<x-dates-rates.filters.sticky-currency />
	</x-dates-rates.filters.sticky>

	<x-drawer id="dates-rates-filters" animation_direction="up" class="dates-rates__drawer">
		<x-drawer.header>
			<h3>{{ __( 'Filters' , 'qrk' ) }}</h3>
		</x-drawer.header>

		<x-drawer.body>
			<x-accordion>
				<x-accordion.item id="filters-accordion-seasons">
					<x-accordion.item-handle title="Region & Season" />
					<x-accordion.item-content>
						<x-dates-rates.filters.seasons :seasons="$filter_data['seasons']" />
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item id="filters-accordion-expeditions">
					<x-accordion.item-handle title="Expedition" />
					<x-accordion.item-content>
						<x-dates-rates.filters.expeditions :expeditions="$filter_data['expeditions']" />
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item id="filters-accordion-adventure-options">
					<x-accordion.item-handle title="Adventure Options (with availability)" />
					<x-accordion.item-content>
						<x-dates-rates.filters.adventure-options :adventure_options="$filter_data['adventure_options']" />
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item id="filters-accordion-months">
					<x-accordion.item-handle title="Departure Month" />
					<x-accordion.item-content>
						<x-dates-rates.filters.departure-months :months="$filter_data['months']" />
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item id="filters-accordion-durations">
					<x-accordion.item-handle title="Duration of Voyage (days)" />
					<x-accordion.item-content>
						<x-dates-rates.filters.durations :durations="$filter_data['durations']" />
					</x-accordion.item-content>
				</x-accordion.item>
				<x-accordion.item id="filters-accordion-ships">
					<x-accordion.item-handle title="Ship" />
					<x-accordion.item-content>
						<x-dates-rates.filters.ships :ships="$filter_data['ships']" />
					</x-accordion.item-content>
				</x-accordion.item>
			</x-accordion>
		</x-drawer.body>

		<x-drawer.footer>
			<x-dates-rates.filters.cta-clear />
			<x-dates-rates.filters.cta-view-results />
		</x-drawer.footer>
	</x-drawer>
</div>
