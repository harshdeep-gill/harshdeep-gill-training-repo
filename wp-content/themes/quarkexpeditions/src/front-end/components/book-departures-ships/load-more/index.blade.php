@props( [
	'load_more_text' => __( 'Load More', 'qrk' ),
] )

@php
	// Early return.
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-book-departures-ships-load-more
	class="book-departures-ships__load-more typography-spacing"
	loading="false"
	load-more-text="{{ $load_more_text }}"
>
	{!! $slot !!}

	<div class="book-departures-ships__load-more-button-container">
		<x-button
			size="big"
			appearance="outline"
			class="book-departures-ships__load-more-button"
			:loading="true"
		>
			<x-escape :content="$load_more_text" />
		</x-button>
	</div>
</quark-book-departures-ships-load-more>
