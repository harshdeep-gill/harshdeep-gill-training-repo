@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="info-cards__tag overline">
	<x-escape :content="$text" />
</div>
