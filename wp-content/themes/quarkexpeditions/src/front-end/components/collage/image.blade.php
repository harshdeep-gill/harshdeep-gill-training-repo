@props( [
	'image_id' => 0,
	'size'     => 'small',
	'title'    => '',
] )

@aware( [
	'name' => '',
] )

@php
	if ( empty( $image_id ) || empty( $size ) ) {
		return;
	}

	$classes = [ 'collage__image-item' ];

	if ( ! empty( $size ) && in_array( $size, [ 'small', 'medium', 'large', 'x-large' ], true ) ) {
		$classes[] = 'collage__image-item--' . $size;
	}

	// Build image args.
	if ( 'small' === $size ) {
		$picture = [
			'(min-width: 1280px)' => [ 300, 300 ],
			'(min-width: 768px)'  => [ 864, 516 ],
			'(min-width: 375px)'  => [ 312, 450 ],
		];
	} elseif ( 'medium' === $size ) {
		$picture = [
			'(min-width: 1280px)' => [ 600, 300 ],
			'(min-width: 768px)'  => [ 864, 516 ],
			'(min-width: 375px)'  => [ 312, 450 ],
		];
	} elseif ( 'large' === $size ) {
		$picture = [
			'(min-width: 1280px)' => [ 850, 250 ],
			'(min-width: 768px)'  => [ 864, 516 ],
			'(min-width: 375px)'  => [ 312, 450 ],
		];
	} else {
		$picture = [
			'(min-width: 1280px)' => [ 1000, 500 ],
			'(min-width: 768px)'  => [ 864, 516 ],
			'(min-width: 375px)'  => [ 312, 450 ],
		];
	}

	$image_args = [
		'size' => [
			'width'   => 1200,
			'height'  => 600,
			'picture' => $picture,
		],
		'transform'   => [
			'crop'    => 'lfill',
			'quality' => 90,
		],
	];
@endphp

@if ( ! empty( $name ) )
	<tp-slider-slide @class($classes)>
		<x-media-lightbox
			name="{{ $name ?? '' }}"
			:image_id="$image_id"
			title="{{ $title }}"
		>
			<x-image
				:image_id="$image_id"
				:args="$image_args"
			/>
		</x-media-lightbox>
	</tp-slider-slide>
@endif
