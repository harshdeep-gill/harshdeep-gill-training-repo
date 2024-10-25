@props( [
	'class'  => '',
	'text'   => '',
	'url'    => '#',
	'target' => '',
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
	href="{!! esc_url( $url ) !!}"
	@if ( ! empty( $target ) )
		target="{{ $target }}"
	@endif
>
	<x-escape :content="$text" />
</a>
