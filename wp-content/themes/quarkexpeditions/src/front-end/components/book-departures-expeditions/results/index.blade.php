@props( [
	'count'         => 0,
	'expedition_id' => 0,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// Build load more button text.
	$load_more_text = __( 'View More Departures', 'qrk' );
@endphp

<x-book-departures-expeditions.load-more :load_more_text="$load_more_text">
	<quark-book-departures-expeditions-results
		class="book-departures-expeditions__results"
		partial='book-departures-expeditions'
		selector='.book-departures-expeditions__results'
		expedition-id={{ $expedition_id }}
	>
		{!! $slot !!}
	</quark-book-departures-expeditions-results>
	<div class="book-departures-expeditions__results--loading">
		<x-svg name="spinner" />
	</div>
</x-book-departures-expeditions.load-more>
