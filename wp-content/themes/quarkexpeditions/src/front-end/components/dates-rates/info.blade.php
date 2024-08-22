@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<p class="dates-rates__info body-small">
	<x-escape content="{{ $text }}" />
</p>
