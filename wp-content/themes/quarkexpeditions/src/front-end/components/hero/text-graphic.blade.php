
@props( [
	'image_id' => 0,
	'size'     => 'large', // options -> large, medium, small
] )

@php

	$classes = [ 'hero__text-graphic' ];

	if ( ! empty( $size ) && in_array( $size, [ 'small', 'medium', 'large' ], true ) ) {
		$classes[] = 'hero__text-graphic--size-' . $size;
	}

	// Build image args.
	$image_args = [
		'size' =>       [
			'width'   => 600,
			'height'  => 460,
		],
		'responsive' => [
			'sizes'  => match( $size ) {
				'small'  => [ '320px' ],
				'medium' => [ '430px' ],
				'large'  => [ '640px' ],
			},
			'widths' => [ 230, 320, 430, 640 ],
		],
		'transform' => [
			'crop'    => 'fit',
			'quality' => '100',
		],
	];
@endphp

<div @class($classes)>
	<x-image
		loading="eager"
		fetchpriority="high"
		:image_id="$image_id"
		:args="$image_args"
	/>
</div>
