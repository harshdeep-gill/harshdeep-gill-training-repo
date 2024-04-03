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
			'width'   => 360,
			'height'  => 240,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1280px) 360px', '(min-width: 1024px) 30vw', '(min-width: 576px) 60vw', '100vw' ],
			'widths' => [ 320, 380, 480, 600, 720 ],
		],
	];
@endphp

<figure class="lp-offer-masthead__offer-image">
	<x-image
		:image_id="$image_id"
		:args="$image_args"
	/>
</figure>
