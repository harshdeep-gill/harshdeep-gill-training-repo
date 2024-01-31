@props( [
	'image_id' => 0,
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
				'height' => 40,
			],
			'medium' =>  [
				'width'  => 'auto',
				'height' => 64,
			],
			'large'  =>  [
				'width'  => 'auto',
				'height' => 96,
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
