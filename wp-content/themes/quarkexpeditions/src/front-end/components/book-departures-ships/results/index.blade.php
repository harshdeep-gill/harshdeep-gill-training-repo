@props( [
	'count'   => 0,
	'ship_id' => 0,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// Build load more button text.
	$load_more_text = __( 'View More Departures', 'qrk' );
@endphp

<x-book-departures-ships.load-more :load_more_text="$load_more_text">
	<quark-book-departures-ships-results
		class="book-departures-ships__results"
		partial='book-departures-ships'
		selector='.departure-cards'
		ship-id={{ $ship_id }}
	>
		{!! $slot !!}
	</quark-book-departures-ships-results>
	<div class="book-departures-ships__results--loading">
		<x-svg name="spinner" />
	</div>
</x-book-departures-ships.load-more>
