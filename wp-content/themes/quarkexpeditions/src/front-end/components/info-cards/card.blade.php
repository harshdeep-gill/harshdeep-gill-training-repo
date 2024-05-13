@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'info-cards__card', 'color-context--dark' ];
@endphp

<tp-slider-slide @class( $classes )>
	{!! $slot !!}
</tp-slider-slide>
