@props( [
	'loading_text'   => __( 'Loading...', 'qrk' ),
	'load_more_text' => __( 'Load More', 'qrk' ),
] )

@php
	// Early return.
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-book-departures-expeditions-load-more
	class="book-departures-expeditions__load-more typography-spacing"
	loading="false"
	load-more-text="{{ $load_more_text }}"
	loading-text="{{ $loading_text }}"
>
	{!! $slot !!}

	<div class="book-departures-expeditions__load-more-button-container">
		<x-button
			size="big"
			appearance="outline"
			class="book-departures-expeditions__load-more-button"
			:loading="true"
		>
			<x-escape :content="$load_more_text" />
		</x-button>
	</div>
</quark-book-departures-expeditions-load-more>
