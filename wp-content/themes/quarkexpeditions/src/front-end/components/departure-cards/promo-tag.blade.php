@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<p class="departure-cards__promo-tag">
	<x-escape :content="$text" />
</p>
