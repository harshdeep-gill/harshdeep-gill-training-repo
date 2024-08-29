@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<li class="expedition-cards__option">
	<x-escape :content="$title" />
</li>
