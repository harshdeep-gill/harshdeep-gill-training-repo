@props( [
	'immersive'       => 'none',
	'overlay_opacity' => 0,
	'content_overlap' => true,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'search-hero' ];

	if ( ! empty( $immersive) && in_array( $immersive, [ 'all', 'bottom', 'top' ], true ) ) {
		$classes[] = sprintf( 'search-hero--immersive-%s', $immersive );
	}

	if ( empty( $content_overlap ) ) {
		$classes[] = 'search-hero--content-no-overlap';
	}

	$overlay_opacity = $overlay_opacity / 100;

	$overlay_style = "--search-hero-overlay-background-opacity:$overlay_opacity;";
@endphp

<x-section full_width="true" seamless="true" @class( $classes )>
	<div
		class="search-hero__overlay"
		style={!! $overlay_style !!}
	></div>
	<div class="search-hero__wrap wrap">
		{!! $slot !!}
	</div>
</x-section>
