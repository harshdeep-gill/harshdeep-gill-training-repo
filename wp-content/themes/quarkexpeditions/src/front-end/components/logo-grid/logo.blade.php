@props( [
	'image_id' => 0,
] )

@aware( [
	'size' => 'small',
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}

	$image_args = [
		'size'      => match ( $size ) {
			'small'  =>  [
				'width'  => 60,
				'height' => 60,
			],
			'medium' =>  [
				'width'  => 100,
				'height' => 100,
			],
			'large'  =>  [
				'width'  => 150,
				'height' => 150,
			],
		},
		'transform' => [
			'crop'  => 'fit',
		],
	];
@endphp

<figure class="logo-grid__logo">
	<x-image
		class="logo-grid__img"
		:args="$image_args"
		:image_id="$image_id"
	/>
</figure>
