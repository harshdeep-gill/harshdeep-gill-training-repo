@props( [
	'image_id' => 0,
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}

	// Build image args.
	$image_args = [
		'size' => [
			'width'   => 120,
			'height'  => 120,
		],
		'transform' => [
			'crop'    => 'fill',
			'quality' => 100,
		],
	];
@endphp

<x-image
	class="post-author-info__image"
	loading="eager"
	fetchpriority="high"
	:image_id="$image_id"
	:args="$image_args"
/>
