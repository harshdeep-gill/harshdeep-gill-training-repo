@props( [
	'image_id' => 0,
	'size'     => 'medium',
	'title'    => '',
] )

@aware( [
    'name'     => '',
] )

@php
	if ( empty( $image_id ) || empty( $size ) ) {
		return;
	}

	$classes = [ 'collage__image-item' ];

	if ( ! empty( $size ) && in_array( $size, [ 'small', 'medium', 'large', 'x-large' ], true ) ) {
		$classes[] = 'collage__image-item--' . $size;
	}

	$image_args = [
		'size' => [
			'width'  => 1152,
			'height' => 648,
		],
		'responsive' => [
			'sizes'  => match( $size ) {
				'small'   => [ '(min-width: 360px) 400px', '100vw' ],
				'medium'  => [ '(min-width: 560px) 600px', '100vw' ],
				'large'   => [ '(min-width: 840px) 900px', '100vw' ],
				'x-large' => [ '(min-width: 992px) 1152px', '100vw' ],
			},
			'widths' => [ 360, 450, 576, 768, 992, 1120 ],
		],
		'transform' => [
			'crop' => 'fill',
		]
	];

	// Get the full URL of the image.
	$full_url = quark_dynamic_image_url(
		[
			'id'        => $image_id,
			'transform' => [
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
