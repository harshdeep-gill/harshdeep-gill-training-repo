@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<h5 class="review-cards__card-title">
	<x-escape :content="$title"/>
</h5>
