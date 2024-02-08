@props( [
	'title'  => '',
	'author' => '',
	'rating' => '',
] )

<tp-slider-slide class="review-cards__card">
	@if( ! empty( $rating ) )
		<div class="review-cards__rating">
			<x-rating-stars rating="{{ $rating }}" />
		</div>
	@endif

	@if ( ! empty( $title ) )
		<h5 class="review-cards__card-title"><x-escape :content="$title"/></h5>
	@endif

	<div class="review-cards__card-content">
		@if ( ! empty( $slot ) )
			<div class="review-cards__content">
				{!! $slot !!}
			</div>
		@endif
	</div>

	@if ( ! empty( $author ) )
		<div class="review-cards__author">
			<strong><x-escape :content="$author" /></strong>
		</div>
	@endif
</tp-slider-slide>
