@props( [
	'count' => 0,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// Build departure count text to be displayed.
	$departure_count_text = sprintf( _n( 'Showing %d departure', 'Showing %d departures', $count, 'qrk' ), $count );
@endphp

<div class="book-departures-expeditions__result-count">
	<x-escape :content="$departure_count_text" />
</div>
