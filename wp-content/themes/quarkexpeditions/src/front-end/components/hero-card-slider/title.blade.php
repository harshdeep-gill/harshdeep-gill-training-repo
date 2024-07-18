@props( [
	'class' => '',
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$classes = [ 'hero-card-slider__title', 'h4' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp


<div @class( $classes )><x-escape :content="$title" /></div>
