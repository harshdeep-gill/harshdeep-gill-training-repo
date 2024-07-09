@props( [
	'class'    => '',
	'interval' => 5,
	'arrows'   => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'hero-card-slider', 'color-context--dark' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	$interval_in_milliseconds = ( ! empty( $interval ) ? $interval : 3 ) * 1000;

	$slide_count = quark_get_slot_child_count( $slot );
@endphp


<tp-slider
	@class( $classes )
	swipe="yes"
	behaviour="slide"
	{{-- auto-slide-interval="{!! esc_attr( $interval_in_milliseconds ) !!}" --}}
	infinite="yes"
>
	<tp-slider-track class="hero-card-slider__track">
		<tp-slider-slides class="hero-card-slider__slides">
			{!! $slot !!}
		</tp-slider-slides>
	</tp-slider-track>

	@if ( ! empty( $arrows ) && $slide_count > 1)
		<tp-slider-arrow direction="previous">
			<button class="hero-card-slider__arrow-button hero-card-slider__arrow-button--left">
				<x-svg name="chevron-left" />
			</button>
		</tp-slider-arrow>

		<tp-slider-arrow direction="next">
			<button class="hero-card-slider__arrow-button hero-card-slider__arrow-button--right">
				<x-svg name="chevron-left" />
			</button>
		</tp-slider-arrow>
	@endif

	@if ( $slide_count > 1 )
		<tp-slider-nav>
			@for ($i = 0; $i < $slide_count; $i++)
				<tp-slider-nav-item><button>{{ $i }}</button></tp-slider-nav-item>
			@endfor
		</tp-slider-nav>
	@endif
</tp-slider>
