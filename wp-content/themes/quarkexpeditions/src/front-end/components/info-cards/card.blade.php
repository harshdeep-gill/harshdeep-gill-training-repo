@props( [
	'content_position' => 'bottom',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'info-cards__card', 'color-context--dark' ];

	if ( ! empty( $content_position ) && in_array( $content_position, [ 'bottom', 'top' ], true ) ) {
		$classes[] = 'info-cards__card--content-' . $content_position;
	}
@endphp

<tp-slider-slide @class( $classes )>
	{!! $slot !!}
</tp-slider-slide>
