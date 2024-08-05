@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="book-departures-expeditions__filters">
	<x-form.field class="book-departures-expeditions__filters-currency">
		<x-form.inline-dropdown label="Currency">
			<x-form.option value="USD" label="$ USD" selected="yes">{{ __( '$ USD', 'qrk' ) }}</x-form.option>
			<x-form.option value="CAD" label="$ CAD">{{ __( '$ CAD', 'qrk' ) }}</x-form.option>
			<x-form.option value="AUD" label="$ AUD">{{ __( '$ AUD', 'qrk' ) }}</x-form.option>
			<x-form.option value="GBP" label="£ GBP">{{ __( '$ GBP', 'qrk' ) }}</x-form.option>
		</x-form.inline-dropdown>
	</x-form.field>
	<x-form.field class="book-departures-expeditions__filters-sort">
		<x-form.inline-dropdown label="Sort">
			<x-form.option value="date-now" label="Date (upcoming to later)" selected="yes">
				{{ __( 'Date (upcoming to later)', 'qrk' ) }}
			</x-form.option>
			<x-form.option value="date-later" label="Date (later to upcoming)">
				{{ __( 'Date (later to upcoming)', 'qrk' ) }}
			</x-form.option>
			<x-form.option value="price-low" label="Price (low to high)">
				{{ __( 'Price (low to high)', 'qrk' ) }}
			</x-form.option>
			<x-form.option value="price-high" label="Price (high to low)">
				{{ __( 'Price (high to low)', 'qrk' ) }}
			</x-form.option>
		</x-form.inline-dropdown>
	</x-form.field>
</div>
