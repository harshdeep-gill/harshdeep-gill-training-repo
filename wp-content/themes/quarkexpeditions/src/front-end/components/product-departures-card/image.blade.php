@props( [
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
			'width'   => 360,
			'height'  => 240,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1280px) 360px', '(min-width: 1024px) 50vw', '100vw' ],
			'widths' => [ 320, 380, 480, 600, 720 ],
		],
	];

	// CSS classes for images.
	$classes = [ 'product-departures-card__image' ];
@endphp

<figure @class( $classes )>
	<x-image
		:image_id="$image_id"
		:args="$image_args"
	/>
</figure>
