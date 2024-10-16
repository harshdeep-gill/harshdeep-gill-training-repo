@props( [
	'class' => '',
	'text'  => '',
] )

@php
	$classes = [ 'hero-card-slider__cta-text' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp


<span @class( $classes )>
	<x-escape :content="$text" />
</span>
