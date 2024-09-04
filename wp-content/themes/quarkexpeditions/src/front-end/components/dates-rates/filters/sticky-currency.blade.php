<quark-dates-rates-filter-sticky-currency>
	<x-dates-rates.filters.chip drawer_id="dates-rates-filters-currency" title="{{ __( 'Currency: USD', 'qrk' ) }}" />

	<x-drawer id="dates-rates-filters-currency" animation_direction="up" class="dates-rates__drawer-currency">
		<x-drawer.header>
			<h3>{{ __( 'Currency', 'qrk' ) }}</h3>
		</x-drawer.header>

		<x-drawer.body>
			<quark-dates-rates-filter-currency-radios>
				<x-form.field-group>
					<x-form.radio name="currency" value="USD" label="$ USD" checked />
					<x-form.radio name="currency" value="CAD" label="$ CAD" />
					<x-form.radio name="currency" value="AUD" label="$ AUD" />
					<x-form.radio name="currency" value="GBP" label="$ GBP" />
					<x-form.radio name="currency" value="EUR" label="$ EUR" />
				</x-form.field-group>
			</quark-dates-rates-filter-currency-radios>
		</x-drawer.body>
	</x-drawer>
</quark-dates-rates-filter-sticky-currency>
