@props( [
	'currency' => 'USD',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// All available currencies.
	$currencies = quark_get_template_data( 'currencies' );

	// If no currencies are available, set an empty array.
	if ( ! is_array( $currencies ) || empty( $currencies ) ) {
		$currencies = [];
	}
@endphp

<quark-expedition-search-filters class="expedition-search__filters">
	@if( ! empty( $currencies ) )
		<x-form.field class="expedition-search__filters-currency">
			<x-form.inline-dropdown :label="__( 'Currency', 'qrk' )">

				@foreach ( $currencies as $code => $currency_data )
					@if ( ! is_array( $currency_data ) || empty( $currency_data['symbol'] ) || empty( $currency_data['display'] ) )
						@continue
					@endif

					<x-form.option value="{{ $code }}" label="{{ $currency_data['display'] }}" selected="{{ $currency === $code ? 'yes' : '' }}">
						{{ $currency_data['display'] }}
					</x-form.option>
				@endforeach

			</x-form.inline-dropdown>
		</x-form.field>
	@endif

	<x-form.field class="expedition-search__filters-sort">
		<x-form.inline-dropdown label="{{ __( 'Sort', 'qrk' ) }}">
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
</quark-expedition-search-filters>
