@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<li class="departure-cards__offer">
	<x-escape :content="$title" />
</li>
