@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<p class="dates-rates__item-table-column-title">
	<x-escape content="{{ $text }}" />
</p>
