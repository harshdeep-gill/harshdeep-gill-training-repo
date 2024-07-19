@props( [
	'appearance' => 'light',
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
@endphp

<x-section @class( $classes )>
	{!! $slot !!}
</x-section>
