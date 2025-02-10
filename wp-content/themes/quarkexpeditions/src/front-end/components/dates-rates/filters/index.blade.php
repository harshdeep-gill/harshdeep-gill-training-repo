@props( [
	'filter_data' => [],
	'currency'     => quark_get_template_data( 'default_currency', 'USD' ),
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

			@if ( ! empty( $filter_data['seasons'] ) )
				<x-dates-rates.filters.chip drawer_id="dates-rates-filters" title="{{ __( 'Region & Season', 'qrk' ) }}" accordion_id="filters-accordion-seasons" />
			@endif

			@if ( ! empty( $filter_data['expeditions'] ) )
				<x-dates-rates.filters.chip drawer_id="dates-rates-filters" title="{{ __( 'Expedition', 'qrk' ) }}" accordion_id="filters-accordion-expeditions" />
			@endif

			@if ( ! empty( $filter_data['adventure_options'] ) )
				<x-dates-rates.filters.chip drawer_id="dates-rates-filters" title="{{ __( 'Adventure Options', 'qrk' ) }}" accordion_id="filters-accordion-adventure-options" />
			@endif

			@if ( ! empty( $filter_data['months'] ) )
				<x-dates-rates.filters.chip drawer_id="dates-rates-filters" title="{{ __( 'Departure Month', 'qrk' ) }}" accordion_id="filters-accordion-months" />
			@endif

			@if ( ! empty( $filter_data['durations'] ) )
				<x-dates-rates.filters.chip drawer_id="dates-rates-filters" title="{{ __( 'Duration', 'qrk' ) }}" accordion_id="filters-accordion-durations" />
			@endif

			@if ( ! empty( $filter_data['ships'] ) )
				<x-dates-rates.filters.chip drawer_id="dates-rates-filters" title="{{ __( 'Ship', 'qrk' ) }}" accordion_id="filters-accordion-ships" />
			@endif

		</x-dates-rates.filters.chips>

		<x-dates-rates.filters.currency-dropdown :currency="$currency" />
	</div>

	<x-dates-rates.filters.selected />

	<x-dates-rates.filters.sticky>
		<x-dates-rates.filters.sticky-filter drawer_id="dates-rates-filters" accordion_id="filters-accordion-seasons" />
		<x-dates-rates.filters.sticky-currency :currency="$currency" />
	</x-dates-rates.filters.sticky>

	<x-drawer id="dates-rates-filters" animation_direction="up" class="dates-rates__drawer">
		<x-drawer.header>
			<h3>{{ __( 'Filters' , 'qrk' ) }}</h3>
		</x-drawer.header>

		<x-drawer.body>
			<x-dates-rates.filters.inputs-container>
				<x-dates-rates.filters.accordion :filter_data="$filter_data" />
			</x-dates-rates.filters.inputs-container>
		</x-drawer.body>

		<x-drawer.footer>
			<x-dates-rates.filters.cta-clear />
			<x-dates-rates.filters.cta-view-results />
		</x-drawer.footer>
	</x-drawer>
</div>
