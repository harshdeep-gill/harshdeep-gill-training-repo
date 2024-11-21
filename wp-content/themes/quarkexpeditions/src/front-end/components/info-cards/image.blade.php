@props( [
	'image_id' => 0,
] )

@php
	// Return if the image id is empty.
	if ( empty( $image_id ) ) {
		return;
	}

	$classes = [ 'info-cards__image' ];

	$image_args = [
		'size' => [
			'width'  => 352,
			'height' => 520,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1024px) 736px', '400px' ],
			'widths' => [ 400, 800 ],
		],
		'transform' => [
			'crop' => 'fill',
		]
	];
@endphp

<figure class="info-cards__image-wrap">
	<x-image
		:image_id="$image_id"
		:args="$image_args"
		@class( $classes )
	/>
</figure>
