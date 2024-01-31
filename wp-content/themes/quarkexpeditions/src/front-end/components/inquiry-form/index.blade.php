@props( [
	'class'  => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'inquiry-form' ];

	if ( ! empty( $class ) ) {
	    $classes[] = $class;
	}
@endphp

<div @class( $classes )>
	{!! $slot !!}
</div>
