@props( [
	'desktop_carousel' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// Get slide count.
	$slide_count = quark_get_slot_child_count( $slot );

	// Converting values in true and false.
	if ( ! empty( $desktop_carousel )  ) {
		$desktop_carousel = 'true';
	} else {
		$desktop_carousel = 'false';
	}
@endphp

<tp-slider class="icon-info-grid__carousel" flexible-height="no" infinite="yes" swipe="yes">
	<tp-slider-track class="icon-info-grid__track">
		<tp-slider-slides class="icon-info-grid__slides-container" desktop-carousel="{{ $desktop_carousel }}" >
			{!! $slot !!}
		</tp-slider-slides>
	</tp-slider-track>

	@if ( $slide_count > 1 )
		<div class="icon-info-grid__nav" desktop-carousel="{{ $desktop_carousel }}">
			<tp-slider-arrow direction="previous">
				<button class="icon-info-grid__arrow-button icon-info-grid__arrow-button--left">
					<x-svg name="chevron-left" />
				</button>
			</tp-slider-arrow>

			<tp-slider-arrow direction="next">
				<button class="icon-info-grid__arrow-button icon-info-grid__arrow-button--right">
					<x-svg name="chevron-left" />
				</button>
			</tp-slider-arrow>
		</div>
	@endif
</tp-slider>
