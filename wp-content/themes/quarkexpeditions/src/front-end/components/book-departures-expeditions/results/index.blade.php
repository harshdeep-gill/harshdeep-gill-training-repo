@props( [
	'count'         => 0,
	'partial'       => '',
	'selector'      => '',
	'expedition_id' => 0,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// Build load more button text.
	$load_more_text = __( sprintf( 'View More Departures (%d)', $count ), 'qrk' );
@endphp

<x-book-departures-expeditions.load-more :load_more_text="$load_more_text">
	<quark-book-departures-expeditions-results
		class="book-departures-expeditions__results"
		data-partial="{{ $partial }}"
		data-selector="{{ $selector }}"
		data-expedition-id={{ $expedition_id }}
	>
		{!! $slot !!}
	</quark-book-departures-expeditions-results>
	<div class="book-departures-expeditions__results--loading">
		<x-svg name="spinner" />
	</div>
</x-book-departures-expeditions.load-more>
