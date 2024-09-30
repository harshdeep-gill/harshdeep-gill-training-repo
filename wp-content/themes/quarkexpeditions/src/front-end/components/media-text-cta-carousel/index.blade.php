@php
	if ( empty( $slot ) ) {
		return;
	}

	// Slide count.
	$slide_count = quark_get_slot_child_count( $slot );
@endphp

<x-section>
	<tp-slider
		class="media-text-cta-carousel"
		swipe="yes"
		infinite="yes"
		flexible-height="yes"
	>
		<tp-slider-track class="media-text-cta-carousel__track">
			<tp-slider-slides class="media-text-cta-carousel__slides">
				{!! $slot !!}
			</tp-slider-slides>
		</tp-slider-track>

		@if ( $slide_count > 1 )
			<div class="media-text-cta-carousel__arrows">
				<tp-slider-arrow direction="previous">
					<button class="media-text-cta-carousel__arrow-button media-text-cta-carousel__arrow-button--left">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>

				<tp-slider-arrow direction="next">
					<button class="media-text-cta-carousel__arrow-button media-text-cta-carousel__arrow-button--right">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>
			</div>
		@endif
	</tp-slider>
</x-section>
