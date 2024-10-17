@props( [
	'class'               => '',
	'request_a_quote_url' => '',
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
		<div class="product-options-cards__navigation">
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

			<div class="product-options-cards__help">
				@if ( ! empty( $request_a_quote_url ) )
					<p class="product-options-cards__help-text">{{ __( 'Not ready to book?', 'qrk' ) }} <a href="{!! esc_url( $request_a_quote_url ) !!}">{{ __( 'Request a quote', 'qrk' ) }}</a></p>
				@endif
				<x-product-options-cards.phone-number />
			</div>
		</div>
	@endif
</tp-slider>
