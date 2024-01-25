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
				'width'  => 'auto',
				'height' => 32,
			],
			'medium' =>  [
				'width'  => 'auto',
				'height' => 40,
			],
			'large'  =>  [
				'width'  => 'auto',
				'height' => 64,
			],
		},
		'transform' => [
			'crop'    => 'fit',
			'quality' => '100',
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
