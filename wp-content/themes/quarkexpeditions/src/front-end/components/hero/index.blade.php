@props( [
	'immersive'       => 'no',
	'text_align'      => '',
	'overlay_opacity' => 0,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'hero' ];

	if ( ! empty( $immersive) && in_array( $immersive, [ 'all', 'bottom', 'top' ], true ) ) {
		$classes[] = sprintf( 'hero--immersive-%s', $immersive );
	}

	if ( ! empty( $text_align ) && in_array( $text_align, [ 'center', 'left' ], true ) ) {
		$classes[] = 'hero--text-' . $text_align;
	} else {
		$classes[] = 'hero--text-left';
	}

	$overlay_opacity = $overlay_opacity / 100;

	$overlay_style = "--hero-overlay-background-opacity:$overlay_opacity;";
@endphp

<x-section full_width="true" seamless="true" @class( $classes )>
	<div
		class="hero__overlay"
		style={!! $overlay_style !!}
	></div>
	<div class="hero__wrap">
		{!! $slot !!}
	</div>
</x-section>
