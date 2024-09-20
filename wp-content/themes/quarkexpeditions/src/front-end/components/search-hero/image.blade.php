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
			'width'   => 1400,
			'height'  => 800,
			'picture' => [
				'(min-width: 1600px)' => [ 1920, 1080 ],
				'(min-width: 1400px)' => [ 1600, 900 ],
				'(min-width: 1280px)' => [ 1400, 800 ],
				'(min-width: 1024px)' => [ 1200, 800 ],
				'(min-width: 768px)'  => [ 900, 550 ],
				'(min-width: 500px)'  => [ 700, 550 ],
				'(min-width: 375px)'  => [ 550, 550 ],
			],
		],
		'transform' => [
			'crop'    => 'lfill',
			'quality' => 90,
		],
	];
@endphp

 <x-image
	class="search-hero__image"
	loading="eager"
	fetchpriority="high"
	:image_id="$image_id"
	:args="$image_args"
/>
