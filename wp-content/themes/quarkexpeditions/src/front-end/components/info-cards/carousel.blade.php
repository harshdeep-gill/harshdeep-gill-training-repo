@props( [
	'layout'          => 'grid',
	'mobile_carousel' => true,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// Get slide count.
	$slide_count = quark_get_slot_child_count( $slot );
@endphp

<div class="info-cards__carousel">
	<tp-slider
		class="info-cards__slider"
		swipe="yes"
		infinite="yes"
	>
		<tp-slider-track class="info-cards__track">
			<tp-slider-slides class="info-cards__slides" total-slides="{{ $slide_count }}" >
				{!! $slot !!}
			</tp-slider-slides>
		</tp-slider-track>

		@if ( $slide_count > 1 )
			<div
				class="info-cards__nav"
				@if ( ! empty( $layout ) && in_array( $layout, [ 'grid', 'collage', 'carousel' ], true ) )
					data-layout="{{ $layout }}"
				@endif

				@if ( ! empty( $mobile_carousel ) )
					data-mobile-carousel="{{ $mobile_carousel }}"
				@endif
			>
				<tp-slider-arrow direction="previous">
					<button class="info-cards__arrow-button info-cards__arrow-button--left">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>

				<tp-slider-arrow direction="next">
					<button class="info-cards__arrow-button info-cards__arrow-button--right">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>
			</div>
		@endif
	</tp-slider>
</div>
