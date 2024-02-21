@props( [
	'immersive' => false,
	'layout'    => 'row',
	'size'      => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'hero' ];

	if ( ! empty( $immersive) && true === boolval( $immersive ) ) {
		$classes[] = 'hero--immersive';
	}

	if ( ! empty( $size ) && 'big' === $size ) {
		$classes[] = 'hero--big';
	}

	$hero_wrap_classes = [ 'hero__wrap' ];

	if ( ! empty( $layout ) && 'column' === $layout ) {
		$hero_wrap_classes[] = 'hero__wrap--column';
	}
@endphp

<x-section full_width="true" seamless="true" @class( $classes )>
	<div @class( $hero_wrap_classes )>
		{!! $slot !!}
	</div>
</x-section>
