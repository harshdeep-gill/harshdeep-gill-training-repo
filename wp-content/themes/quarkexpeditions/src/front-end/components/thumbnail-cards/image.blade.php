@props( [
	'image_id'    => 0,
	'size'        => 'medium',
	'orientation' => 'portrait'
] )

@php
	// Return if the image id is empty.
	if ( empty( $image_id ) || empty( $size ) ) {
		return;
	}

	$classes = [ 'thumbnail-cards__image' ];

	if ( ! empty( $size ) && in_array( $size, [ 'small', 'medium', 'large' ], true ) ) {
		$classes[] = 'thumbnail-cards__image--size-' . $size;
	}

	if ( ! empty( $orientation ) && in_array( $orientation, [ 'portrait', 'landscape' ], true ) ) {
		$classes[] = 'thumbnail-cards__image--orient-' . $orientation;
	}

	// Key to map size and orientation.
	$size_orientation_key = $size . '-' . $orientation;

	$image_args = [
		'size' => [
			'width'  => 240,
			'height' => 320,
		],
		'responsive' => [
			'sizes'  => match( $size_orientation_key ) {
				'small-portrait'   => [ '180px' ],
				'small-landscape'  => [ '250px' ],
				'medium-portrait'  => [ '220px' ],
				'medium-landscape' => [ '350px' ],
				'large-portrait'   => [ '400px' ],
			},
			'widths' => [ 180, 220, 250, 350, 400 ],
		],
		'transform' => [
			'crop' => 'fill',
		]
	];
@endphp

<figure class="thumbnail-cards__image-wrap">
	<x-image
		:image_id="$image_id"
		:args="$image_args"
		@class( $classes )
	/>
</figure>
