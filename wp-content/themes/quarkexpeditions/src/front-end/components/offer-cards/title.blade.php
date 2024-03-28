@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<h2 class="offer-cards__title h4">
	<x-escape :content="$title" />
</h2>
