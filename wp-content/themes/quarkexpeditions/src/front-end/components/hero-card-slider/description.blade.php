@props( [
	'class' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'hero-card-slider__description' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp


<div @class( $classes )>
	<x-content :content="$slot" />
</div>
