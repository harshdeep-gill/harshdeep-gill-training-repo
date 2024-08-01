@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="hero__savings-tag overline">
	<x-escape :content="$text" />
</div>
