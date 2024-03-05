@props( [
	'total_reviews' => '',
	'review_rating' => '',
] )

@php
	if ( empty( $total_reviews ) && empty( $review_rating ) ) {
		return;
	}
@endphp

<div class="product-cards__reviews">
	<x-rating-stars rating="{{ $review_rating }}" />
	<x-escape :content="$total_reviews" />
</div>
