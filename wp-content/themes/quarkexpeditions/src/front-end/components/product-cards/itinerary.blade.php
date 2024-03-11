@props( [
	'departure_date_text' => '',
	'duration_text'       => '',
] )

@php
	if ( empty( $departure_date_text ) || empty( $duration_text ) ) {
		return;
	}
@endphp

<div class="product-cards__itinerary">
	<span class="product-cards__departure-date-text">
		<x-escape :content="$departure_date_text" />
	</span>

	<span class="product-cards__duration-text">
		{{ $duration_text }}
	</span>
</div>
