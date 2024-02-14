@php
	if ( empty( $slot ) ) {
		return;
	}

	// Get slide count.
	$slide_count = quark_get_slot_child_count( $slot );
@endphp

<div class="collage__carousel">
	<tp-slider class="collage__slider" swipe="yes" infinite="yes">
		<tp-slider-track class="collage__track">
			<tp-slider-slides class="collage__slides-container">
				{!! $slot !!}
			</tp-slider-slides>
		</tp-slider-track>

		@if ( $slide_count > 1 )
			<div class="collage__nav">
				<tp-slider-arrow direction="previous">
					<button class="collage__arrow-button collage__arrow-button--left">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>

				<tp-slider-arrow direction="next">
					<button class="collage__arrow-button collage__arrow-button--right">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>
			</div>
		@endif
	</tp-slider>
</div>
