@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<h2 class="listing-cards__title h3">
	<x-escape :content="$title" />
</h2>
