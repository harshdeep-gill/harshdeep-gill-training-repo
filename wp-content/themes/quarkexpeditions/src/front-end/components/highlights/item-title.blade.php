@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<h2 class="highlights__item-title h5">
	<x-escape :content="$title" />
</h2>
