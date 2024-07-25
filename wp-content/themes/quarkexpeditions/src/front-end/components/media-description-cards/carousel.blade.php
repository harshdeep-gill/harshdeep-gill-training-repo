@php
	if ( empty( $slot ) ) {
		return;
	}

	// Get slide count.
	$slide_count = quark_get_slot_child_count( $slot );
@endphp

<tp-slider class="media-description-cards__carousel" flexible-height="no" infinite="yes" swipe="yes">
	<tp-slider-track class="media-description-cards__track">
		<tp-slider-slides class="media-description-cards__slides-container">
			{!! $slot !!}
		</tp-slider-slides>
	</tp-slider-track>

	@if ( $slide_count > 1 )
		<div class="media-description-cards__nav">
			<tp-slider-arrow direction="previous">
				<button class="media-description-cards__arrow-button media-description-cards__arrow-button--left">
					<x-svg name="chevron-left" />
				</button>
			</tp-slider-arrow>

			<tp-slider-arrow direction="next">
				<button class="media-description-cards__arrow-button media-description-cards__arrow-button--right">
					<x-svg name="chevron-left" />
				</button>
			</tp-slider-arrow>
		</div>
	@endif
</tp-slider>
