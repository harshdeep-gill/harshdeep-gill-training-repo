@props( [
	'immersive'       => false,
	'text_align'      => '',
	'dark_mode'       => false,
	'overlay_opacity' => 0,
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

	$overlay_opacity = $overlay_opacity / 100;

	$overlay_style = "background-color:rgba(0,0,0,$overlay_opacity);";
@endphp

<x-section full_width="true" seamless="true" @class( $classes )>
	<quark-hero-overlay
		class="hero__overlay"
		data-style={!! $overlay_style !!}
	></quark-hero-overlay>
	<div class="hero__wrap">
		{!! $slot !!}
	</div>
</x-section>
