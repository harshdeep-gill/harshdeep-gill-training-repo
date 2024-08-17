@props( [
	'url'  => '',
	'text' => __( 'Request a Quote', 'qrk' ),
] )

@php
	if ( empty( $url ) ) {
		return;
	}
@endphp

<x-button
    href="{{ $url }}"
    size="big"
    class="expedition-cta__cta"
>
    <x-escape :content="$text" />
</x-button>
