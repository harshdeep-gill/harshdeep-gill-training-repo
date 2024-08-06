@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<li class="departure-cards__option">
	<x-escape :content="$title" />
</li>
