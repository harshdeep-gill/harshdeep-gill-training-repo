@props( [
	'class' => '',
] )

@php
	$classes = [ 'featured-media-accordions' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<x-section>
	<quark-featured-media-accordions @class( $classes )>
		<x-two-columns>
			{!! $slot !!}
		</x-two-columns>
	</quark-featured-media-accordions>
</x-section>
