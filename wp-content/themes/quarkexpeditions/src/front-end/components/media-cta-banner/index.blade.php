@props( [
	'appearance'       => 'light',
	'background_color' => 'gray',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'media-cta-banner' ];

	if ( 'light' === $appearance ) {
		$classes[] = 'media-cta-banner--light';
	}

	if ( 'dark' === $appearance ) {
		$classes[] = 'media-cta-banner--dark color-context--dark';
	}

	if ( 'solid' === $appearance ) {
		$classes[] = 'media-cta-banner--solid';
		
		if ( 'gray' === $background_color ) {
			$classes[] = 'media-cta-banner--has-background-gray';
		}
	}
@endphp

<x-section @class( $classes )>
	{!! $slot !!}
</x-section>
