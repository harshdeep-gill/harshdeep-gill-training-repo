@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="review-cards__author-details">
	<x-escape :content="$text" />
</div>
