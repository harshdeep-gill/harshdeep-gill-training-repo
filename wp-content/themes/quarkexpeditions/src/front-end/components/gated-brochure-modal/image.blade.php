@props( [
	'image_id' => 0,
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}

	$image_args = [
		'size' => [
			'width'   => 352,
			'height'  => 566,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1280px) 352px', '(min-width: 1024px) 50vw', '100vw' ],
			'widths' => [ 380, 480, 600, 800 ],
		],
	];
@endphp

<div class="gated-brochure-modal__image-wrap">
	<x-image
		class="gated-brochure-modal__image"
		:args="$image_args"
		:image_id="$image_id"
	/>
</div>
