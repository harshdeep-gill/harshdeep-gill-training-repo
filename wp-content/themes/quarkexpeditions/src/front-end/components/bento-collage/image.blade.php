@props( [
	'image_id' => 0,
	'size'     => 'medium',
] )

@aware( [
	'size' => '',
] )

@php
	if ( empty( $image_id ) || empty( $size ) ) {
		return;
	}

	$classes = [ 'bento-collage__image' ];

	if ( ! empty( $size ) && in_array( $size, [ 'small', 'medium', 'large', 'full' ], true ) ) {
		$classes[] = 'bento-collage__image--' . $size;
	}

	// Build image args.
	if ( 'small' === $size ) {
		$width = 352;
		$height = 520;
		$picture = [
			'(min-width: 1024px)' => [ 352, 520 ],
			'(min-width: 375px)'  => [ 416, 520 ],
		];
	} elseif ( 'medium' === $size ) {
		$width = 544;
		$height = 520;
		$picture = [
			'(min-width: 1024px)' => [ 544, 520 ],
			'(min-width: 375px)'  => [ 416, 520 ],
		];
	} elseif ( 'large' === $size ) {
		$width = 736;
		$height = 520;
		$picture = [
			'(min-width: 1024px)' => [ 736, 520 ],
			'(min-width: 375px)'  => [ 416, 520 ],
		];
	} else {
		$width = 1120;
		$height = 520;
		$picture = [
			'(min-width: 1024px)' => [ 1120, 520 ],
			'(min-width: 375px)'  => [ 416, 520 ],
		];
	}

	$image_args = [
		'size' => [
			'width'   => $width,
			'height'  => $height,
			'picture' => $picture,
		],
		'transform'   => [
			'crop'    => 'fill',
			'quality' => 90,
		],
	];
@endphp

<figure class="bento-collage__image-wrap">
	<x-image
		:image_id="$image_id"
		:args="$image_args"
		@class( $classes )
	/>
</figure>
