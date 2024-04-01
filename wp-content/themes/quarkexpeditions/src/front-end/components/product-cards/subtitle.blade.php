@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<p class="product-cards__subtitle">
	<x-content :content="$title" />
</p>
