@props( [
	'appearance' => '',
] )

@php
	if( empty( $slot ) ) {
		return;
	}

	$classes = [ 'expedition-details', 'typography-spacing' ];

	if ( 'dark' === $appearance ) {
		$classes[] = 'color-context--dark';
	}
@endphp

<div @class( $classes )>
	{!! $slot !!}
</div>
