@props( [
	'image_id' => 0,
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}

	$image_args = [
		'size' => [
			'width'   => 540,
			'height'  => 360,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1280px) 540px', '(min-width: 1024px) 50vw', '100vw' ],
			'widths' => [ 380, 480, 600, 800 ],
		],
	];
@endphp

<x-image
	class="media-cta-banner__image"
	:args="$image_args"
	:image_id="$image_id"
/>
