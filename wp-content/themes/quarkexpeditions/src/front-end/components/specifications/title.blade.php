@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<h2 class="specifications__title h4">
	<x-escape :content="$title" />
</h2>
