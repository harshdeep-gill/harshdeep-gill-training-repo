@props( [
	'review_title' => '',
	'author'       => '',
	'rating'       => '',
] )

<tp-slider-slide class="reviews-carousel__slide">
	@if ( ! empty( $review_title ) )
		<h4 class="reviews-carousel__slide-title"><x-escape :content="$review_title"/></h4>
	@endif
	<div class="reviews-carousel__slide-content">
		@if ( ! empty( $slot ) )
			<div class="reviews-carousel__content">
				{!! $slot !!}
			</div>
		@endif

		<div class="reviews-carousel__author">
			@if ( ! empty( $author ) )
				<div class="reviews-carousel__name">
					@if ( ! empty( $author ) )
						<strong class="reviews-carousel__name"><x-escape :content="$author" /></strong>
					@endif
				</div>
			@endif
			@if( ! empty( $rating ) )
				<div class="reviews-carousel__rating">
					<div class="reviews-carousel__rating-inner">
						<x-rating-stars rating="{{ $rating }}" />
					</div>
				</div>
			@endif
		</div>
	</div>
</tp-slider-slide>
