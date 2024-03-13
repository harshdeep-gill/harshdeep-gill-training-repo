@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<div class="product-cards__subtitle">
	<x-content :content="$title" />
</div>
