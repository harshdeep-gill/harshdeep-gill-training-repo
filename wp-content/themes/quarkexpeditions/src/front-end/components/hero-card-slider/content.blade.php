@props( [
	'class' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'hero-card-slider__content' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp


<div class="hero-card-slider__overlay"></div>
<div @class( $classes )>
	{!! $slot !!}
</div>
