
@props( [
	'image_id' => 0,
] )

@php
	// Build image args.
	$image_args = [
		'size' =>       [
			'width'   => 600,
			'height'  => 600,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1024px) 600px', '100vw' ],
			'widths' => [ 350, 500, 600, 700, 800 ],
		],
		'transform' => [
			'crop'    => 'fit',
			'quality' => '100',
		],
	];

	$classes = [ 'hero__text-graphic' ];
@endphp

<div @class($classes)>
	<x-image
		:image_id="$image_id"
		:args="$image_args"
	/>
</div>
