@props( [
	'class' => '',
	'text'  => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}

	$classes = [ 'hero-card-slider__overline', 'overline' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp


<div @class( $classes )><x-escape :content="$text" /></div>
