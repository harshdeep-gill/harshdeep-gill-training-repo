@props( [
	'image_id' => 0,
] )

@php
	// Return if the image id is empty.
	if ( empty( $image_id ) ) {
		return;
	}

	// Image arguments.
	$image_args = [
		'size' =>       [
			'width'   => 736,
			'height'  => 262,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1280px) 736px', '100vw' ],
			'widths' => [ 320, 380, 480, 600, 720, 900, 1200, 1600 ],
		],
		'transform' => [
			'crop' => 'fit',
		],
	];
@endphp

<figure class="lp-offer-masthead__offer-image">
	<x-image
		:image_id="$image_id"
		:args="$image_args"
		loading="eager"
		fetchpriority="high"
	/>
</figure>
