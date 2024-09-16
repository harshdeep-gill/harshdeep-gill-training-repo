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

			@php
				$label = sprintf( '%s %s', $currency_data['symbol'],  $currency_data['display'] );
			@endphp

			<x-form.radio name="currency" value="{{ $code }}" label="{{ $label }}" checked="{{ $currency === $code ? 'checked' : '' }}" />
		@endforeach

	</x-form.field-group>
</quark-dates-rates-filter-currency-radios>
