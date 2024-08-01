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
	{!! $slot !!}
</quark-featured-media-accordions>
