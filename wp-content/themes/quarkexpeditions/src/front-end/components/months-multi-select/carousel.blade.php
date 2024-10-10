@props( [
	'button_text' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// Get slide count.
	$slide_count = quark_get_slot_child_count( $slot );
@endphp

<tp-slider class="months-multi-select__slider" swipe="yes" infinite="yes">
	<tp-slider-track class="months-multi-select__track">
		<tp-slider-slides>
			{!! $slot !!}
		</tp-slider-slides>
	</tp-slider-track>

	<div class="months-multi-select__nav">
		@if ( ! empty( $button_text ) )
			<button class="months-multi-select__reset-button">{{ $button_text }}</button>
		@endif

		@if ( $slide_count > 1 )
			<div class="months-multi-select__nav-arrows">
				<tp-slider-arrow direction="previous">
					<button class="months-multi-select__arrow-button months-multi-select__arrow-button--left">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>

				<tp-slider-arrow direction="next">
					<button class="months-multi-select__arrow-button months-multi-select__arrow-button--right">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>
			</div>
		@endif
	</div>
</tp-slider>
