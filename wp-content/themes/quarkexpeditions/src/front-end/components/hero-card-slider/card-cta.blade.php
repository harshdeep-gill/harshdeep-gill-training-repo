@props( [
	'class' => '',
	'text'  => '',
	'url'   => '#',
] )

@php
	if ( empty( $text ) || empty( $url ) ) {
		return;
	}

	$classes = [ 'hero-card-slider__card-cta' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp


<a
	@class( $classes )
	href="{!! esc_url( $url ) !!}">
	<x-escape :content="$text" />
</a>
