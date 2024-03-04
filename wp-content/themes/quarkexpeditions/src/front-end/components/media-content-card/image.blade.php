@props( [
	'image_id' => 0,
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}

	$image_args = [
		'size' => [
			'width'   => 760,
			'height'  => 480,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1280px) 760px', '100vw' ],
			'widths' => [ 320, 380, 480, 600, 760 ],
		],
	];
@endphp

<div class="media-content-card__image-wrap">
	<x-image
		class="media-content-card__image"
		:image_id="$image_id"
		:args="$image_args"
	/>
</div>
