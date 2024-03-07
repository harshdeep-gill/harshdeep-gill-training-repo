@props( [
	'departure_date' => '',
	'duration'       => '',
] )

@php
	if ( empty( $departure_date ) || empty( $duration ) ) {
		return;
	}
@endphp

<div class="product-cards__itinerary">
	<span class="product-cards__departure-date">
		{{ __( 'Departing', 'qrk' ) }} <x-escape :content="$departure_date" />
	</span>

	<span class="product-cards__duration">
		| {{ $duration }} {{ __( 'Days', 'qrk' ) }}
	</span>
</div>
