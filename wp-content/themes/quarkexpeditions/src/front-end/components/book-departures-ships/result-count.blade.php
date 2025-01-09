@props( [
	'count' => 0,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// Build departure count text to be displayed.
	$departure_count_text = _n( 'departure', 'departures', $count, 'qrk' );
@endphp

<div class="book-departures-ships__result-count">
	{{ __( 'Showing', 'qrk' ) }}
	<span class="book-departures-ships__result-count-value">{{ $count ?? 0 }}</span>
	{{ $departure_count_text ?? 'departures' }}
</div>
