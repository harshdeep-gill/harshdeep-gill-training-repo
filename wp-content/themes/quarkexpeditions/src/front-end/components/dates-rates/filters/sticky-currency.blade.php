<quark-dates-rates-filter-sticky-currency>
	<x-dates-rates.filters.chip drawer_id="dates-rates-filters-currency" title="{{ __( 'Currency: USD', 'qrk' ) }}" />

	<x-drawer id="dates-rates-filters-currency" animation_direction="up" class="dates-rates__drawer-currency">
		<x-drawer.header>
			<h3>{{ __( 'Currency', 'qrk' ) }}</h3>
		</x-drawer.header>

		<x-drawer.body>
			<x-dates-rates.filters.currency-radios />
		</x-drawer.body>
	</x-drawer>
</quark-dates-rates-filter-sticky-currency>
