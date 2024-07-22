@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<p class="highlights__item-title">
	<x-escape :content="$title" />
</p>
