@props( [
	'url'         => '',
	'size'        => 'medium',
	'orientation' => 'portrait',
	'image_id'    => 0,
	'video_id'    => 0,
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

	// Video arguments.
	$video_args = [
		'transform' => [
			'width'   => 700,
			'height'  => 700,
			'crop'    => 'fit',
			'quality' => 100,
		],
	];
@endphp

<tp-slider-slide @class( $classes )>
	<x-maybe-link href="{{ $url }}">
		@if ( ! empty( $image_id ) )
			<x-thumbnail-cards.image
				:image_id="$image_id"
				:size="$size"
				:orientation="$orientation"
			/>
		@elseif ( ! empty( $video_id ) )
			<x-video
				:video_id="$video_id"
				:args="$video_args"
				:loop="true"
				:controls="false"
			/>
		@endif

		{!! $slot !!}
	</x-maybe-link>
</tp-slider-slide>
