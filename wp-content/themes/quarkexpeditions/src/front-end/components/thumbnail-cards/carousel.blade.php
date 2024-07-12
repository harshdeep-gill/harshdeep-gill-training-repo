@props( [
	'is_carousel' => 'false',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// Get slide count.
	$slide_count = quark_get_slot_child_count( $slot );
@endphp

<div class="thumbnail-cards__carousel">
	<tp-slider
		class="thumbnail-cards__slider"
		swipe="yes"
		infinite="yes"
	>
		<tp-slider-track class="thumbnail-cards__track">
			<tp-slider-slides class="thumbnail-cards__slides">
				{!! $slot !!}
			</tp-slider-slides>
		</tp-slider-track>

		@if ( $slide_count > 1 )
			<div class="thumbnail-cards__nav" data-is-carousel="{{ $is_carousel }}">
				<tp-slider-arrow direction="previous">
					<button class="thumbnail-cards__arrow-button thumbnail-cards__arrow-button--left">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>

				<tp-slider-arrow direction="next">
					<button class="thumbnail-cards__arrow-button thumbnail-cards__arrow-button--right">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>
			</div>
		@endif
	</tp-slider>
</div>
