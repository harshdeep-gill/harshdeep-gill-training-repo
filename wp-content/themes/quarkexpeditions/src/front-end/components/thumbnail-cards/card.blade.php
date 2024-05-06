@props( [
	'url'         => '#',
	'size'        => 'medium',
	'orientation' => 'portrait',
	'image_id'    => 0,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'thumbnail-cards__card' ];

	if ( ! empty( $size ) && in_array( $size, [ 'small', 'medium', 'large' ], true ) ) {
		$classes[] = 'thumbnail-cards__card--size-' . $size;
	}

	if ( ! empty( $orientation ) && in_array( $orientation, [ 'portrait', 'landscape' ], true ) ) {
		$classes[] = 'thumbnail-cards__card--orient-' . $orientation;
	}
@endphp

<tp-slider-slide @class( $classes )>
	<x-maybe-link href="{{ $url }}">
		<x-thumbnail-cards.image
			:image_id="$image_id"
			:size="$size"
			:orientation="$orientation"
		/>
		{!! $slot !!}
	</x-maybe-link>
</tp-slider-slide>
