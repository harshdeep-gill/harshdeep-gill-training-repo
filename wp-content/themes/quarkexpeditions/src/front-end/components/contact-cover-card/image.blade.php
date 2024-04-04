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
			'width'   => 1120,
			'height'  => 516,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1280px) 1120px', '100vw' ],
			'widths' => [ 400, 600, 900, 1200, 1440, 1600 ],
		],
	];

	// CSS classes for images.
	$classes = [ 'contact-cover-card__image-wrap' ];
@endphp

<figure @class( $classes )>
	<x-image
		:image_id="$image_id"
		:args="$image_args"
		class="contact-cover-card__image"
	/>
</figure>
