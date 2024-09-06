
@props( [
	'image_id' => 0,
] )

@php
	// Build image args.
	$image_args = [
		'size'       => [
			'width'   => 400,
			'height'  => 400,
		],
		'responsive' => [
			'sizes'  => ['(min-width: 1024px) 400px', '100vw'],
			'widths' => [250, 300, 350],
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
