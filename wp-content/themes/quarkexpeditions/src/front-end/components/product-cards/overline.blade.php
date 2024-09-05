@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<p class="product-cards__overline overline">
	<x-escape :content="$text" />
</p>
