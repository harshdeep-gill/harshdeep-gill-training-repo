@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<p class="header__nav-item-featured-title h4">
	<x-escape :content="$title" />
</p>
