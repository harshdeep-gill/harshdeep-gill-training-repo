@props( [
	'class'    => '',
	'image_id' => 0,
] )

@php
	// Return if the image id is empty.
	if ( empty( $image_id ) ) {
		return;
	}

	// Image arguments.
	$image_args = [
		'size' =>       [
			'width'   => 544,
			'height'  => 584,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1280px) 544px', '312px' ],
			'widths' => [ 312, 544 ],
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
