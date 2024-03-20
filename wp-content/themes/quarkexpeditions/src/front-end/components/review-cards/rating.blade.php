@props( [
	'rating' => '',
] )

@php
	if ( empty( $rating ) ) {
		return;
	}
@endphp

<div class="review-cards__rating">
	<x-rating-stars rating="{{ $rating }}" />
</div>