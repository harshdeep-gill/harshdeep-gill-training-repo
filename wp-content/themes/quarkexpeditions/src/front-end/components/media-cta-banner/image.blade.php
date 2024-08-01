@props( [
	'image_id' => 0,
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}

	$image_args = [
		'size' => [
			'width'  => 1120,
			'height' => 400,
		],
		'transform' => [
			'crop' => 'lfill',
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1280px) 1120px', '100vw' ],
			'widths' => [ 400, 600, 900, 1120 ],
		],
	];
@endphp

<x-image
	class="media-cta-banner__image"
	:args="$image_args"
	:image_id="$image_id"
/>
