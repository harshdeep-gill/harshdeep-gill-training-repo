@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-book-departures-ships-filters class="book-departures-ships__filters">
	<x-form.field class="book-departures-ships__filters-currency">
		<x-form.inline-dropdown label="Currency">
			<x-form.option value="USD" label="$ USD" selected="yes">{{ __( '$ USD', 'qrk' ) }}</x-form.option>
			<x-form.option value="CAD" label="$ CAD">{{ __( '$ CAD', 'qrk' ) }}</x-form.option>
			<x-form.option value="AUD" label="$ AUD">{{ __( '$ AUD', 'qrk' ) }}</x-form.option>
			<x-form.option value="GBP" label="£ GBP">{{ __( '£ GBP', 'qrk' ) }}</x-form.option>
			<x-form.option value="EUR" label="€ EUR">{{ __( '€ EUR', 'qrk' ) }}</x-form.option>
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
