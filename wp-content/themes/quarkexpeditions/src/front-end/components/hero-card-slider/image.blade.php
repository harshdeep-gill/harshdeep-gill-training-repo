@props( [
	'class'    => '',
	'image_id' => 0,
] )

@php
	// Return if the image id is empty.
	if ( empty( $image_id ) ) {
		return;
	}

	// Image arguments
	$image_args = [
		'size' => [
			'width'   => 312,
			'height'  => 208,
			'picture' => [
				'(min-width: 1024px)' => [ 544, 584 ],
				'(min-width: 768px)'  => [ 1024, 577 ],
			],
		],
		'transform' => [
			'crop'    => 'fill',
			'gravity' => 'auto',
			'quality' => 90,
		],
	];


	// CSS classes for images.
	$classes = [ 'hero-card-slider__image' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<figure @class( $classes )>
	<x-image
		:image_id="$image_id"
		:args="$image_args"
	/>
</figure>
