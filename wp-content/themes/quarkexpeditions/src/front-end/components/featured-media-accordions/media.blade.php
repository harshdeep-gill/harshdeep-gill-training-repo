@props( [
	'class' => '',
] )

@php
	$classes = [ 'featured-media-accordions__media' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<x-two-columns.column @class( $classes )>
	{!! $slot !!}
</x-two-columns.column>
