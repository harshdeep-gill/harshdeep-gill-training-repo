@props( [
	'image_id'     => 0,
	'aspect_ratio' => 'landscape',
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}

	if ( 'square' === $aspect_ratio ) {
		$image_height = 546;
	} else {
		$image_height = 360;
	}

	$image_args = [
		'size' => [
			'width'   => 546,
			'height'  => $image_height,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1280px) 546px', '(min-width: 1024px) 50vw', '100vw' ],
			'widths' => [ 380, 480, 600, 800 ],
		],
	];
@endphp

<div class="media-text-cta__media-wrap">
	<x-image
		class="media-text-cta__image"
		:args="$image_args"
		:image_id="$image_id"
	/>

	{!! $slot !!}
</div>
