@props( [
	'immersive' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'hero' ];

	if ( ! empty( $immersive) && true === boolval( $immersive ) ) {
		$classes[] = 'hero--immersive';
	}
@endphp

<x-section full_width="true" seamless="true" @class( $classes )>
	<div class="hero__wrap">
		{!! $slot !!}
	</div>
</x-section>
