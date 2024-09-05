<quark-dates-rates-filter-currency-dropdown class="dates-rates__filter-currency">
	<x-form.select>
		<x-form.option value="USD" label="$ USD" selected="yes">{{ __( '$ USD', 'qrk' ) }}</x-form.option>
		<x-form.option value="CAD" label="$ CAD">{{ __( '$ CAD', 'qrk' ) }}</x-form.option>
		<x-form.option value="AUD" label="$ AUD">{{ __( '$ AUD', 'qrk' ) }}</x-form.option>
		<x-form.option value="GBP" label="£ GBP">{{ __( '£ GBP', 'qrk' ) }}</x-form.option>
		<x-form.option value="EUR" label="€ EUR">{{ __( '€ EUR', 'qrk' ) }}</x-form.option>
	</x-form.select>
</quark-dates-rates-filter-currency-dropdown>
