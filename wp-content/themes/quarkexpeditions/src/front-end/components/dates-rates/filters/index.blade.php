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

			<x-dates-rates.filters.currency>
				<x-form.select>
					<x-form.option value="USD" label="$ USD" selected="yes">{{ __( '$ USD', 'qrk' ) }}</x-form.option>
					<x-form.option value="CAD" label="$ CAD">{{ __( '$ CAD', 'qrk' ) }}</x-form.option>
					<x-form.option value="AUD" label="$ AUD">{{ __( '$ AUD', 'qrk' ) }}</x-form.option>
					<x-form.option value="GBP" label="£ GBP">{{ __( '£ GBP', 'qrk' ) }}</x-form.option>
					<x-form.option value="EUR" label="€ EUR">{{ __( '€ EUR', 'qrk' ) }}</x-form.option>
				</x-form.select>
			</x-dates-rates.filters.currency>
		</div>

		<x-dates-rates.filters.selected />

		<x-dates-rates.filters.sticky>
			<x-dates-rates.filters.chip drawer_id="dates-rates-filters" title="Filter" accordion_id="filters-accordion-seasons" type="sticky-filter" />
			<x-dates-rates.filters.chip drawer_id="dates-rates-filters-currency" type="currency" title="Currency: USD" />
		</x-dates-rates.filters.sticky>

		<x-drawer id="dates-rates-filters-currency" animation_direction="up" class="dates-rates__drawer-currency">
			<x-drawer.header>
				<h3>Currency</h3>
			</x-drawer.header>

			<x-drawer.body>
				<x-form.field-group>
					<x-form.radio name="currency" value="USD" label="$ USD" checked />
					<x-form.radio name="currency" value="CAD" label="$ CAD" />
					<x-form.radio name="currency" value="AUD" label="$ AUD" />
					<x-form.radio name="currency" value="GBP" label="$ GBP" />
					<x-form.radio name="currency" value="EUR" label="$ EUR" />
				</x-form.field-group>
			</x-drawer.body>
		</x-drawer>

		<x-drawer id="dates-rates-filters" animation_direction="up" class="dates-rates__drawer">
			<x-drawer.header>
				<h3>{{ __( 'Filters' , 'qrk' ) }}</h3>
			</x-drawer.header>

			<x-drawer.body>
				<x-accordion>
					<x-accordion.item id="filters-accordion-seasons">
						<x-accordion.item-handle title="Region & Season" />
						<x-accordion.item-content>
							<x-form.field-group>
								@if ( ! empty( $filter_data['seasons'] ) )
									@foreach ( $filter_data['seasons'] as $filter_value => $filter_label )
										<x-form.checkbox name="seasons" :label="$filter_label" :value="$filter_value" />
									@endforeach
								@endif
							</x-form.field-group>
						</x-accordion.item-content>
					</x-accordion.item>
					<x-accordion.item id="filters-accordion-expeditions">
						<x-accordion.item-handle title="Expedition" />
						<x-accordion.item-content>
							<x-form.field-group>
								@if ( ! empty( $filter_data['expeditions'] ) )
									@foreach ( $filter_data['expeditions'] as $filter_value => $filter_label )
										<x-form.checkbox name="expeditions" :label="$filter_label" :value="$filter_value" />
									@endforeach
								@endif
							</x-form.field-group>
						</x-accordion.item-content>
					</x-accordion.item>
					<x-accordion.item id="filters-accordion-adventure-options">
						<x-accordion.item-handle title="Adventure Options (with availability)" />
						<x-accordion.item-content>
							<x-form.field-group>
								@if ( ! empty( $filter_data['adventure_options'] ) )
									@foreach ( $filter_data['adventure_options'] as $filter_value => $filter_label )
										<x-form.checkbox name="adventure_options" :label="$filter_label" :value="$filter_value" />
									@endforeach
								@endif
							</x-form.field-group>
						</x-accordion.item-content>
					</x-accordion.item>
					<x-accordion.item id="filters-accordion-months">
						<x-accordion.item-handle title="Departure Month" />
						<x-accordion.item-content>
							<x-form.field-group>
								@if ( ! empty( $filter_data['months'] ) )
									@foreach ( $filter_data['months'] as $filter_value => $filter_label )
										<x-form.checkbox name="months" :label="$filter_label" :value="$filter_value" />
									@endforeach
								@endif
							</x-form.field-group>
						</x-accordion.item-content>
					</x-accordion.item>
					<x-accordion.item id="filters-accordion-durations">
						<x-accordion.item-handle title="Duration of Voyage (days)" />
						<x-accordion.item-content>
							<x-form.field-group>
								@if ( ! empty( $filter_data['durations'] ) )
									@foreach ( $filter_data['durations'] as $filter_value => $filter_label )
										<x-form.checkbox name="durations" :label="$filter_label" :value="$filter_value" />
									@endforeach
								@endif
							</x-form.field-group>
						</x-accordion.item-content>
					</x-accordion.item>
					<x-accordion.item id="filters-accordion-ships">
						<x-accordion.item-handle title="Ship" />
						<x-accordion.item-content>
							<x-form.field-group>
								@if ( ! empty( $filter_data['ships'] ) )
									@foreach ( $filter_data['ships'] as $filter_value => $filter_label )
										<x-form.checkbox name="ships" :label="$filter_label" :value="$filter_value" />
									@endforeach
								@endif
							</x-form.field-group>
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
