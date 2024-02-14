@props( [
	'image_id' => 0,
	'size'     => '',
	'title'    => '',
] )

@aware( [
    'name'     => '',
] )

@php
	if ( empty( $image_id ) || empty( $slot ) ) {
		return;
	}

	$classes = [ 'collage__image-item' ];

	if ( ! empty( $size ) && in_array( $size, [ 'small', 'medium', 'large', 'x-large' ], true ) ) {
		$classes[] = 'collage__image-item--' . $size;
	}

	$image_args = [
		'size' => [
			'width'  => 1280,
			'height' => 720,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 720px) 870px', '100vw' ],
			'widths' => [ 320, 375, 450, 500, 576, 650, 980, 1024, 1400, 1600 ],
		],
		'transform' => [
			'crop' => 'lfill',
		]
	];

	// Get the full URL of the image.
	$full_url = quark_dynamic_image_url(
		[
			'id'        => $image_id,
			'transform' => [
				'width'   => 1400,
				'height'  => 788,
				'quality' => 90,
			],
		]
	);
@endphp


@if ( ! empty( $name ) )
	<tp-slider-slide @class($classes)>
		<x-media-lightbox
			name="{{ $name ?? '' }}"
			path="{{ $full_url }}"
			title="{{ $title }}"
		>
			<x-image
				:image_id="$image_id"
				:args="$image_args"
			/>
		</x-media-lightbox>
	</tp-slider-slide>
@endif
