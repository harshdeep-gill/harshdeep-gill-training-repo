@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<h2 class="product-cards__title h4">
	<x-content :content="$title" />
</h2>
