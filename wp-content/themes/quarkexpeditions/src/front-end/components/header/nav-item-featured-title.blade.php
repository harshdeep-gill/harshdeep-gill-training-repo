@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<h2 class="header__nav-item-featured-title h4">
	<x-content :content="$title" />
</h2>
