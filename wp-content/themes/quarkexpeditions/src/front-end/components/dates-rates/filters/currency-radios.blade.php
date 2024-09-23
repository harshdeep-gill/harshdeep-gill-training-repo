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

<quark-dates-rates-filter-currency-radios>
	<x-form.field-group>

		@foreach ( $currencies as $code => $currency_data )
			@if ( ! is_array( $currency_data ) || empty( $currency_data['symbol'] ) || empty( $currency_data['display'] ) )
				@continue
			@endif

			<x-form.radio name="currency" value="{{ $code }}" label="{{ $currency_data['display'] }}" checked="{{ $currency === $code ? 'checked' : '' }}" />
		@endforeach

	</x-form.field-group>
</quark-dates-rates-filter-currency-radios>
