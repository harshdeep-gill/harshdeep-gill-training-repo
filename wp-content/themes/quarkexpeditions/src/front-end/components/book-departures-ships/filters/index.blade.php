@props( [
	'currency' => 'USD',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// All available currencies.
	$currencies = quark_get_template_data('currencies');

	// If no currencies are available, set an empty array.
	if ( ! is_array( $currencies ) || empty( $currencies ) ) {
		$currencies = [];
	}
@endphp

<quark-book-departures-ships-filters class="book-departures-ships__filters">
	<x-form.field class="book-departures-ships__filters-currency">
		<x-form.inline-dropdown label="Currency">
			
			@foreach ( $currencies as $code => $label )
				<x-form.option value="{{ $code }}" label="{{ $label }}" selected="{{ $currency === $code ? 'yes' : '' }}">
					{{ $label }}
				</x-form.option>
			@endforeach

		</x-form.inline-dropdown>
	</x-form.field>
	<x-form.field class="book-departures-ships__filters-sort">
		<x-form.inline-dropdown label="Sort">
			<x-form.option value="date-now" label="{{ __( 'Date (upcoming to later)', 'qrk' ) }}" selected="yes">
				{{ __( 'Date (upcoming to later)', 'qrk' ) }}
			</x-form.option>
			<x-form.option value="date-later" label="{{ __( 'Date (later to upcoming)', 'qrk' ) }}">
				{{ __( 'Date (later to upcoming)', 'qrk' ) }}
			</x-form.option>
			<x-form.option value="price-low" label="{{ __( 'Price (low to high)', 'qrk' )  }}">
				{{ __( 'Price (low to high)', 'qrk' ) }}
			</x-form.option>
			<x-form.option value="price-high" label="{{ __( 'Price (high to low)', 'qrk' )  }}">
				{{ __( 'Price (high to low)', 'qrk' ) }}
			</x-form.option>
		</x-form.inline-dropdown>
	</x-form.field>
</quark-book-departures-ships-filters>
