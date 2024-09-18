@props( [
	'layout' => 'carousel',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// Get slide count.
	$slide_count = quark_get_slot_child_count( $slot );
@endphp

<div class="product-cards__carousel">
	<tp-slider
		class="product-cards__slider"
		swipe="yes"
		infinite="yes"
	>
		<tp-slider-track class="product-cards__track">
			<tp-slider-slides class="product-cards__slides">
				{!! $slot !!}
			</tp-slider-slides>
		</tp-slider-track>

		@if ( $slide_count > 1 )
			<div
				class="product-cards__nav"
				@if ( ! empty( $layout ) && in_array( $layout, [ 'grid', 'carousel' ], true ) )
					data-layout="{{ $layout }}"
				@endif
			>
				<tp-slider-arrow direction="previous">
					<button class="product-cards__arrow-button product-cards__arrow-button--left">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>

				<tp-slider-arrow direction="next">
					<button class="product-cards__arrow-button product-cards__arrow-button--right">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>
			</div>
		@endif
	</tp-slider>
</div>
