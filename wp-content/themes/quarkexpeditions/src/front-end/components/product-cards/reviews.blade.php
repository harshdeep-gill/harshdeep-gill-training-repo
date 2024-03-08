@props( [
	'review_rating'      => '',
	'total_reviews_text' => '',
] )

@php
	if ( empty( $total_reviews_text ) && empty( $review_rating ) ) {
		return;
	}
@endphp

<div class="product-cards__reviews">
	<x-rating-stars rating="{{ $review_rating }}" />
	<span class="product-cards__reviews-text"><x-escape :content="$total_reviews_text" /></span>
</div>
