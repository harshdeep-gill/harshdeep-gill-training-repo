@php
	if ( empty( $slot ) ) {
		return;
	}

	// Get slide count.
	$slide_count = quark_get_slot_child_count( $slot );
@endphp

<tp-slider class="reviews-carousel__slider" swipe="yes" infinite="yes">
	<tp-slider-track class="reviews-carousel__track">
		<tp-slider-slides>
			{!! $slot !!}
		</tp-slider-slides>
	</tp-slider-track>

	<div class="reviews-carousel__nav">
		<tp-slider-arrow direction="previous">
			<button class="reviews-carousel__arrow-button reviews-carousel__arrow-button--left">
				<x-svg name="chevron-left" />
			</button>
		</tp-slider-arrow>

		<tp-slider-arrow direction="next">
			<button class="reviews-carousel__arrow-button reviews-carousel__arrow-button--right">
				<x-svg name="chevron-left" />
			</button>
		</tp-slider-arrow>
	</div>
</tp-slider>
