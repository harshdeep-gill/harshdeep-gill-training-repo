@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<h2 class="expedition-details__title h1">
	<x-escape content="{{ $title }}"/>
</h2>
