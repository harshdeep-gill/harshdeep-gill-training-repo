@props( [
	'image_id' => 0,
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}

	$image_args = [
		'size' => [
			'width'   => 400,
			'height'  => 400,
		],
		'transform' => [
			'crop' => 'fit',
		],
	];
@endphp

<figure class="lp-offer-masthead__logo">
	<x-image
		:image_id="$image_id"
		:args="$image_args"
		loading="eager"
		fetchpriority="high"
	/>
</figure>
