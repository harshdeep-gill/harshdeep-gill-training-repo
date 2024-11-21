@props( [
	'color' => 'blue',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'hero__overline', 'overline' ];

	if ( ! empty( $color ) ) {
		$colors = [ 'blue', 'black', 'white' ];

		if ( in_array( $color, $colors, true ) ) {
			$classes[] = sprintf( 'hero__overline-color--%s', $color );
		}
	}
@endphp

<div @class( $classes) >
	<x-content :content="$slot"/>
</div>
