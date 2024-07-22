@props( [
	'class' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'product-options-cards__cards' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	$slide_count = quark_get_slot_child_count( $slot );
@endphp

<tp-slider
	@class( $classes )
	swipe="yes"
	behaviour="slide"
	infinite="yes"
>
	<tp-slider-track class="product-options-cards__track">
		<tp-slider-slides class="product-options-cards__slides">
			{!! $slot !!}
		</tp-slider-slides>
	</tp-slider-track>

	@if ( $slide_count > 1)
		<div class="product-options-cards__arrows">
			<tp-slider-arrow direction="previous">
				<button class="product-options-cards__arrow-button product-options-cards__arrow-button--left">
					<x-svg name="chevron-left" />
				</button>
			</tp-slider-arrow>
			<tp-slider-arrow direction="next">
				<button class="product-options-cards__arrow-button product-options-cards__arrow-button--right">
					<x-svg name="chevron-left" />
				</button>
			</tp-slider-arrow>
		</div>
	@endif
</tp-slider>
