@props( [
	'review_rating' => '',
	'total_reviews' => '',
] )

@php
	if ( empty( $total_reviews ) && empty( $review_rating ) ) {
		return;
	}
@endphp

<div class="product-cards__reviews">
	<x-rating-stars rating="{{ $review_rating }}" />
	<span class="product-cards__reviews-text"><x-escape :content="$total_reviews" /></span>
</div>
