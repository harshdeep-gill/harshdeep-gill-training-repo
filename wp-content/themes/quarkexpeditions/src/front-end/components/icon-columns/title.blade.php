@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<p class="icon-columns__title">
	<x-escape :content="$title" />
</p>
