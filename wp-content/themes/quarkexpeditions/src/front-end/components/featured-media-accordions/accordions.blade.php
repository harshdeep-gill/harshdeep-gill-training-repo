@props( [
	'class' => '',
] )

@php
	$classes = [ 'featured-media-accordions__accordions' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<x-two-columns.column @class( $classes )>
	<x-accordion>
		{!! $slot !!}
	</x-accordion>
</x-two-columns.column>
