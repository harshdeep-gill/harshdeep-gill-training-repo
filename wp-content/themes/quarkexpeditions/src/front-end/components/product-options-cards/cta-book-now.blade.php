@props( [
	'url'  => '',
	'text' => __( 'Book Expedition Now', 'qrk' ),
] )

@php
	if ( empty( $url ) ) {
		return;
	}
@endphp

<x-button
	:href="$url"
	size="big"
	class="product-options-cards__cta-book-now"
>
	<x-escape :content="$text" />
</x-button>
