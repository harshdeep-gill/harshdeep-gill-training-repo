@props( [
	'color' => 'blue',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'search-hero__overline', 'overline' ];

	if ( ! empty( $color ) ) {
		$colors = [ 'blue', 'black' ];

		if ( in_array( $color, $colors, true ) ) {
			$classes[] = sprintf( 'search-hero__overline-color--%s', $color );
		}
	}
@endphp

<div @class( $classes) >
	<x-content :content="$slot"/>
</div>
