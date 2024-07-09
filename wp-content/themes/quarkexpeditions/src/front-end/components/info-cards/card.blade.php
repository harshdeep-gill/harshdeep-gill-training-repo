@props( [
	'url' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'info-cards__card', 'color-context--dark' ];
@endphp

<tp-slider-slide @class( $classes )>
	<x-maybe-link
		:href="$url"
		fallback_tag="div"
	>
		{!! $slot !!}
	</x-maybe-link>
</tp-slider-slide>
