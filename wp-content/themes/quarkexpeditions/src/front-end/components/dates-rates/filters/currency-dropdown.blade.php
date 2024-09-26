@props( [
	'currency' => quark_get_template_data( 'default_currency', 'USD' ),
] )

@php
	// All available currencies.
	$currencies = quark_get_template_data( 'currencies' );

	// If no currencies are available, set an empty array.
	if ( ! is_array( $currencies ) || empty( $currencies ) ) {
		$currencies = [];
	}
@endphp

<quark-dates-rates-filter-currency-dropdown class="dates-rates__filter-currency">
	<x-form.select>

		@foreach ( $currencies as $code => $currency_data )
			@if ( ! is_array( $currency_data ) || empty( $currency_data['symbol'] ) || empty( $currency_data['display'] ) )
				@continue
			@endif

			<x-form.option value="{{ $code }}" label="{{ $currency_data['display'] }}" selected="{{ $currency === $code ? 'yes' : '' }}">
				{{ $currency_data['display'] }}
			</x-form.option>
		@endforeach

	</x-form.select>
</quark-dates-rates-filter-currency-dropdown>
