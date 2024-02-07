@php
	if ( empty( $slot ) ) {
		return;
	}

	// Get slide count.
	$slide_count = quark_get_slot_child_count( $slot );
@endphp

<tp-slider class="review-cards__slider" swipe="yes" infinite="yes">
	<tp-slider-track class="review-cards__track">
		<tp-slider-slides>
			{!! $slot !!}
		</tp-slider-slides>
	</tp-slider-track>

	<div class="review-cards__nav">
		<tp-slider-arrow direction="previous">
			<button class="review-cards__arrow-button review-cards__arrow-button--left">
				<x-svg name="chevron-left" />
			</button>
		</tp-slider-arrow>

		<tp-slider-arrow direction="next">
			<button class="review-cards__arrow-button review-cards__arrow-button--right">
				<x-svg name="chevron-left" />
			</button>
		</tp-slider-arrow>
	</div>
</tp-slider>
