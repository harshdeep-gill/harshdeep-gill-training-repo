
@props( [
	'image_id' => 0,
] )

@php
	// Build image args.
	$image_args = [
		'size' => [
			'width'   => 475,
			'height'  => 475,
			'picture' => [
			],
		],
		'transform' => [
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
