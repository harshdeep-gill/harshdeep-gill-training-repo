@props( [
	'url'  => '',
	'text' => __( 'Download Brochure', 'qrk' ),
] )

@php
	if ( empty( $url ) ) {
		return;
	}
@endphp

<x-button
    href="{{ $url }}"
    color="black"
    size="big"
    class="itinerary-details__download-button"
>
    <x-escape :content="$text" />
</x-button>
