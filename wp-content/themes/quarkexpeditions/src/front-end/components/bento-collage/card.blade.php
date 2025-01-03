@props( [
	'url'    => '',
	'size'   => 'medium',
	'target' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'bento-collage__card', 'color-context--dark' ];

	if ( ! empty( $size ) && in_array( $size, [ 'small', 'medium', 'large', 'full' ], true ) ) {
		$classes[] = 'bento-collage__card--' . $size;
	}
@endphp

<tp-slider-slide @class( $classes )>
	<x-maybe-link :href="$url" class="bento-collage__maybe-link" fallback_tag="div" target="{{ $target }}">
		{!! $slot !!}
	</x-maybe-link>
</tp-slider-slide>
