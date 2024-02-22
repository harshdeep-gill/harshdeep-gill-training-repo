@props( [
	'variant' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'icon-columns' ];

	if ( ! empty( $variant ) && in_array( $variant, [ 'light', 'dark' ], true ) ) {
		if( 'dark' === $variant ) {
			$classes[] = 'color-context--dark';
		} else {
			$classes[] = 'icon-columns--light';
		}
	}
@endphp

<div @class( $classes )>
	{!! $slot !!}
</div>
