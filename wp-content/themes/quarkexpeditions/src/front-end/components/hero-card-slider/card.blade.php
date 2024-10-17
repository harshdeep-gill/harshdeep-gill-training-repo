@props( [
	'class' => '',
	'url'   => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'hero-card-slider__card' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp


<tp-slider-slide @class( $classes )>
	@if ( ! empty( $url ) )
		<a href="{{ $url }}" class="hero-card-slider__card-link">
	@endif

	{!! $slot !!}

	@if ( ! empty( $url ) )
		</a>
	@endif
</tp-slider-slide>
