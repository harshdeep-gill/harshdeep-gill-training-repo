@props( [
	'title'  => '',
	'author' => '',
	'rating' => '',
] )

<tp-slider-slide class="reviews-carousel__slide">
	@if ( ! empty( $title ) )
		<h4 class="reviews-carousel__slide-title"><x-escape :content="$title"/></h4>
	@endif
	<div class="reviews-carousel__slide-content">
		@if ( ! empty( $slot ) )
			<div class="reviews-carousel__content">
				{!! $slot !!}
			</div>
		@endif

		<div class="reviews-carousel__author-rating">
			@if ( ! empty( $author ) )
				<div class="reviews-carousel__name">
					<strong><x-escape :content="$author" /></strong>
				</div>
			@endif
			@if( ! empty( $rating ) )
				<div class="reviews-carousel__rating">
					<x-rating-stars rating="{{ $rating }}" />
				</div>
			@endif
		</div>
	</div>
</tp-slider-slide>
