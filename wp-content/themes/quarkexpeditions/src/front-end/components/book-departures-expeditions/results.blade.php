@props( [
	'count'    => 0,
	'partial'  => '',
	'selector' => '',
	'payload'  => [],
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// Build load more button text.
	$load_more_text = __( sprintf( 'View More Departures (%d)', $count ), 'qrk' );
@endphp

<x-load-more
	:load_more_text="$load_more_text"
	partial="{{ $partial }}"
	selector="{{ $selector }}"
	:payload="$payload"
>
	<div
		class="book-departures-expeditions__results"
		data-partial="{{ $partial }}"
		data-selector="{{ $selector }}"
		data-payload={{ wp_json_encode( $payload ) }}
	>
		{!! $slot !!}
	</div>
	<div class="book-departures-expeditions__results--loading">
		<x-svg name="spinner" />
	</div>
</x-load-more>
