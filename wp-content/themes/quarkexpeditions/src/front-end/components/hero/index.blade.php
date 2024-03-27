@props( [
	'immersive'  => false,
	'text_align' => '',
	'dark_mode'  => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'hero' ];

	if ( ! empty( $immersive) && true === boolval( $immersive ) ) {
		$classes[] = 'hero--immersive';
	}

	if ( ! empty( $text_align ) && in_array( $text_align, [ 'center', 'left' ], true ) ) {
		$classes[] = 'hero--text-' . $text_align;
	} else {
		$classes[] = 'hero--text-left';
	}

	if ( !empty( $dark_mode ) ) {
		$classes[] = 'color-context--dark';
	}
@endphp

<x-section full_width="true" seamless="true" @class( $classes )>
	<div class="hero__wrap">
		{!! $slot !!}
	</div>
</x-section>
