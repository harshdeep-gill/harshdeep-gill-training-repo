@props( [
	'class' => '',
] )

@php
	$classes = [ 'featured-media-accordions' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<quark-featured-media-accordions @class( $classes )>
	<x-two-columns>
		{!! $slot !!}
	</x-two-columns>
</quark-featured-media-accordions>
