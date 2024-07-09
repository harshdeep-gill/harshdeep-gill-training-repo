@props( [
	'class' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'hero-card-slider__card' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp


<tp-slider-slide @class( $classes )>
	<div class="hero-card-slider__overlay"></div>
	{!! $slot !!}
</tp-slider-slide>
