@props([
	'currency' => quark_get_template_data( 'default_currency', 'USD' ),
])

@php
	$drawer_title = sprintf( '%s: %s', __( 'Currency', 'qrk' ), $currency );
@endphp

<quark-dates-rates-filter-sticky-currency>
	<x-dates-rates.filters.chip drawer_id="dates-rates-filters-currency" title="{{ $drawer_title }}" />

	<x-drawer id="dates-rates-filters-currency" animation_direction="up" class="dates-rates__drawer-currency">
		<x-drawer.header>
			<h3>{{ __( 'Currency', 'qrk' ) }}</h3>
		</x-drawer.header>

		<x-drawer.body>
			<x-dates-rates.filters.currency-radios :currency="$currency" />
		</x-drawer.body>
	</x-drawer>
</quark-dates-rates-filter-sticky-currency>
