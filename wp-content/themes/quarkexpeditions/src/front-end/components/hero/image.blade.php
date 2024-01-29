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
			'width'   => 1440,
			'height'  => 720,
			'picture' => [
				'(min-width: 1600px)' => [ 1920, 720 ],
				'(min-width: 1400px)' => [ 1600, 720 ],
				'(min-width: 1280px)' => [ 1400, 720 ],
				'(min-width: 1024px)' => [ 1200, 720 ],
				'(min-width: 768px)'  => [ 900, 500 ],
				'(min-width: 500px)'  => [ 700, 500 ],
				'(min-width: 375px)'  => [ 500, 500 ],
			],
		],
		'transform' => [
			'crop'    => 'lfill',
			'quality' => 90,
		],
	];
@endphp

 <x-image
	class="hero__image"
	loading="eager"
	fetchpriority="high"
	:image_id="$image_id"
	:args="$image_args"
/>
