@props( [
	'image_id' => 0,
	'width'  => 'auto',
	'height' => 'auto'
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}

	$image_args = [
		'size' => [
			'width'   => $width,
			'height'  => $height,
		],
		'transform' => [
			'crop' => 'fit',
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
