@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<p class="expedition-cards__promo-tag overline">
	<x-escape :content="$text" />
</p>
