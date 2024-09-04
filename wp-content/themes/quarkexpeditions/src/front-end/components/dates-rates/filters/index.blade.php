@php
	if ( empty( $slot ) ) {
		return;
	}

	$filter_data = [
		'seasons'            => Quark\Search\Departures\get_region_and_season_search_filter_data(),
		'expeditions'        => Quark\Search\Departures\get_expedition_search_filter_data(),
		'adventure_options'  => Quark\Search\Departures\get_adventure_options_search_filter_data(),
		'months'             => Quark\Search\Departures\get_month_search_filter_data(),
		'durations'          => Quark\Search\Departures\get_duration_search_filter_data(),
		'ships'              => Quark\Search\Departures\get_ship_search_filter_data(),
	];
@endphp

<quark-dates-rates-filters-controller class="dates-rates__filters-controller">
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
							<quark-dates-rates-filter-seasons>
								<x-form.field-group>
									@if ( ! empty( $filter_data['seasons'] ) )
										@foreach ( $filter_data['seasons'] as $filter_value => $filter_label )
											<x-dates-rates.filters.checkbox name="seasons" :label="$filter_label" :value="$filter_value" data-label="{!! $filter_label !!}" />
										@endforeach
									@endif
								</x-form.field-group>
							</quark-dates-rates-filter-seasons>
						</x-accordion.item-content>
					</x-accordion.item>
					<x-accordion.item id="filters-accordion-expeditions">
						<x-accordion.item-handle title="Expedition" />
						<x-accordion.item-content>
							<quark-dates-rates-filter-expeditions>
								<x-form.field-group>
									@if ( ! empty( $filter_data['expeditions'] ) )
										@foreach ( $filter_data['expeditions'] as $filter_value => $filter_label )
											<x-dates-rates.filters.checkbox name="expeditions" :label="$filter_label" :value="$filter_value" />
										@endforeach
									@endif
								</x-form.field-group>
							</quark-dates-rates-filter-expeditions>
						</x-accordion.item-content>
					</x-accordion.item>
					<x-accordion.item id="filters-accordion-adventure-options">
						<x-accordion.item-handle title="Adventure Options (with availability)" />
						<x-accordion.item-content>
							<quark-dates-rates-filter-adventure-options>
								<x-form.field-group>
									@if ( ! empty( $filter_data['adventure_options'] ) )
										@foreach ( $filter_data['adventure_options'] as $filter_value => $filter_label )
											<x-dates-rates.filters.checkbox name="adventure_options" :label="$filter_label" :value="$filter_value" />
										@endforeach
									@endif
								</x-form.field-group>
							</quark-dates-rates-filter-adventure-options>
						</x-accordion.item-content>
					</x-accordion.item>
					<x-accordion.item id="filters-accordion-months">
						<x-accordion.item-handle title="Departure Month" />
						<x-accordion.item-content>
							<quark-dates-rates-filter-departure-months>
								<x-form.field-group>
									@if ( ! empty( $filter_data['months'] ) )
										@foreach ( $filter_data['months'] as $filter_value => $filter_label )
											<x-dates-rates.filters.checkbox name="months" :label="$filter_label" :value="$filter_value" />
										@endforeach
									@endif
								</x-form.field-group>
							</quark-dates-rates-filter-departure-months>
						</x-accordion.item-content>
					</x-accordion.item>
					<x-accordion.item id="filters-accordion-durations">
						<x-accordion.item-handle title="Duration of Voyage (days)" />
						<x-accordion.item-content>
							<quark-dates-rates-filter-durations>
								<x-form.field-group>
									@if ( ! empty( $filter_data['durations'] ) )
										@foreach ( $filter_data['durations'] as $filter_value => $filter_label )
											<x-dates-rates.filters.checkbox name="durations" :label="$filter_label" :value="$filter_value" />
										@endforeach
									@endif
								</x-form.field-group>
							</quark-dates-rates-filter-durations>
						</x-accordion.item-content>
					</x-accordion.item>
					<x-accordion.item id="filters-accordion-ships">
						<x-accordion.item-handle title="Ship" />
						<x-accordion.item-content>
							<quark-dates-rates-filter-ships>
								<x-form.field-group>
									@if ( ! empty( $filter_data['ships'] ) )
										@foreach ( $filter_data['ships'] as $filter_value => $filter_label )
											<x-dates-rates.filters.checkbox name="ships" :label="$filter_label" :value="$filter_value" />
										@endforeach
									@endif
								</x-form.field-group>
							</quark-dates-rates-filter-ships>
						</x-accordion.item-content>
					</x-accordion.item>
				</x-accordion>
			</x-drawer.body>

			<x-drawer.footer>
				<x-dates-rates.filters.cta-clear />
				<x-button class="dates-rates__apply-filters-btn" size="big">View Results (132)</x-button>
			</x-drawer.footer>
		</x-drawer>
	</div>
</quark-dates-rates-filters-controller>
