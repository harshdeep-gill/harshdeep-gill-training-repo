@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<h3 class="departure-cards__title h4">
	<x-escape :content="$title" />
</h3>
