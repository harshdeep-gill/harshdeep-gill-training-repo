@props( [
	'image_id' => 0,
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}

	$classes = [ 'media-carousel__item' ];


	$image_args = [
		'size' => [
			'width'  => 1120,
			'height' => 516,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 992px) 1120px', '100vw' ],
			'widths' => [ 360, 450, 576, 768, 992, 1120 ],
		],
		'transform' => [
			'crop'    => 'fill',
			'quality' => 90,
			'gravity' => 'auto',
		]
	];
@endphp

<tp-slider-slide @class($classes)>
	<x-image
		:image_id="$image_id"
		:args="$image_args"
	/>
</tp-slider-slide>
