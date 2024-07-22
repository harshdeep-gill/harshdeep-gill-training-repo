@props( [
	'class'     => '',
	'image_ids' => [],
] )

@php
	if ( empty( $image_ids ) ) {
		return;
	}

	$classes = [ 'product-options-cards__gallery' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	$image_args = [
		'size' => [
			'width'  => 400,
			'height' => 250,
		],
		'transform' => [
			'crop' => 'fill',
			'quality' => 100
		]
	];

	$slide_count = count( $image_ids );
@endphp

<div @class( $classes )>
	<tp-slider
		class="product-options-cards__gallery-slider"
		swipe="yes"
		behaviour="slide"
		infinite="yes"
	>
		<tp-slider-track class="product-options-cards__gallery-track">
			<tp-slider-slides class="product-options-cards__gallery-slides">
				@foreach ( $image_ids as $image_id )
					<tp-slider-slide class="product-options-cards__gallery-slide">
						<x-image
							:image_id="$image_id"
							:args="$image_args"
						/>
					</tp-slider-slide>
				@endforeach
			</tp-slider-slides>
		</tp-slider-track>

		@if ( $slide_count > 1)
			<div class="product-options-cards__gallery-arrows">
				<tp-slider-arrow direction="previous">
					<button class="product-options-cards__gallery-arrow-button product-options-cards__gallery-arrow-button--left">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>
				<tp-slider-arrow direction="next">
					<button class="product-options-cards__gallery-arrow-button product-options-cards__gallery-arrow-button--right">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>
			</div>
		@endif

		@if ( $slide_count > 1 )
			<tp-slider-nav>
				@for ($i = 0; $i < $slide_count; $i++)
					<tp-slider-nav-item><button>{{ $i }}</button></tp-slider-nav-item>
				@endfor
			</tp-slider-nav>
		@endif
	</tp-slider>

	@if ( ! empty( $slot ) )
		{!! $slot !!}
	@endif
</div>
