@props( [
	'name' => '',
] )

@php
	if ( empty( $name ) ) {
		return;
	}
@endphp

<strong class="review-cards__author">
	<x-escape :content="$name" />
</strong>
