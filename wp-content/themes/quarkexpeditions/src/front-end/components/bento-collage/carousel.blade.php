@php
	if ( empty( $slot ) ) {
		return;
	}

	// Get slide count.
	$slide_count = quark_get_slot_child_count( $slot );
@endphp

<div class="bento-collage__carousel">
	<tp-slider class="bento-collage__slider" swipe="yes" infinite="yes">
		<tp-slider-track class="bento-collage__track">
			<tp-slider-slides class="bento-collage__slides">
				{!! $slot !!}
			</tp-slider-slides>
		</tp-slider-track>

		@if ( $slide_count > 1 )
			<div class="bento-collage__nav">
				<tp-slider-arrow direction="previous">
					<button class="bento-collage__arrow-button bento-collage__arrow-button--left">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>

				<tp-slider-arrow direction="next">
					<button class="bento-collage__arrow-button bento-collage__arrow-button--right">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>
			</div>
		@endif
	</tp-slider>
</div>
