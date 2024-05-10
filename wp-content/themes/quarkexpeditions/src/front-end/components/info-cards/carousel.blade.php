@props( [
	'is_carousel' => false,
	'is_gallery'  => false,
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
			<tp-slider-slides class="info-cards__slides">
				{!! $slot !!}
			</tp-slider-slides>
		</tp-slider-track>

		@if ( $slide_count > 1 )
			<div
				class="info-cards__nav"
				@if ( empty( $is_gallery ) && ! empty( $is_carousel ) )
					data-is-carousel="{{ $is_carousel }}"
				@elseif ( ! empty( $is_gallery ) )
					data-is-gallery="{{ $is_gallery }}"
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
