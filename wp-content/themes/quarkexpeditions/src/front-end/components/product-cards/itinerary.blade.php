@props( [
	'departure_date' => '',
	'duration'       => '',
] )

@php
	if ( empty( $departure_date ) && empty( $duration ) ) {
		return;
	}

	$separator = '';
	// Show separator if both values are available.
	if ( ! empty( $departure_date ) && ! empty( $duration ) ) {
		$separator = ' | ';
	}
@endphp

<div class="product-cards__itinerary">
	<span class="product-cards__departure-date">
		<x-escape :content="$departure_date" />
	</span>
	{{ $separator }}
	<span class="product-cards__duration">
		{{ $duration }}
	</span>
</div>
