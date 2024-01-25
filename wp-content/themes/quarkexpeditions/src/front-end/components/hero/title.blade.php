@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<h1 class="hero__title">
	<x-content :content="$title" />
</h1>
