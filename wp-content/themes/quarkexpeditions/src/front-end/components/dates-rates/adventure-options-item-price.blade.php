@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<p class="dates-rates__adventure-options-item-price">
	<x-escape content="{{ $text }}" />
</p>
