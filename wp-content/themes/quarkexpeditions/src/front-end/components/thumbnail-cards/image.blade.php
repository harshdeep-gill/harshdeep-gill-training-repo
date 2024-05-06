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
				'small-portrait'   => [ '(min-width: 1024px) 180px', '100vw' ],
				'small-landscape'  => [ '(min-width: 1024px) 250px', '100vw' ],
				'medium-portrait'  => [ '(min-width: 1024px) 220px', '100vw' ],
				'medium-landscape' => [ '(min-width: 1024px) 350px', '100vw' ],
				'large-portrait'   => [ '(min-width: 1024px) 400px', '100vw' ],
			},
			'widths' => [ 180, 250, 315, 350, 768, 1120 ],
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
