@props( [
	'image_id'  => '',
	'video_url' => '',
	'size'      => 'medium',
	'title'     => '',
] )

@aware( [
    'name'     => '',
] )

@php
	if ( empty( $image_id ) || empty( $video_url ) ) {
		return;
	}

	$classes = [ 'collage__video-item' ];

	if ( ! empty( $size ) && in_array( $size, [ 'small', 'medium', 'large', 'x-large' ], true ) ) {
		$classes[] = 'collage__video-item--' . $size;
	}

	// Build image args.
	if ( 'small' === $size ) {
		$width = 600;
		$height = 600;
		$picture = [
			'(min-width: 1024px)' => [ 600, 600 ],
			'(min-width: 768px)'  => [ 624, 352 ],
			'(min-width: 375px)'  => [ 546, 308 ],
		];
	} elseif ( 'medium' === $size ) {
		$width = 544;
		$height = 256;
		$picture = [
			'(min-width: 1024px)' => [ 1088, 512 ],
			'(min-width: 768px)'  => [ 624, 352 ],
			'(min-width: 375px)'  => [ 546, 308 ],
		];
	} elseif ( 'large' === $size ) {
		$width = 832;
		$height = 256;
		$picture = [
			'(min-width: 1024px)' => [ 1300, 400 ],
			'(min-width: 768px)'  => [ 624, 352 ],
			'(min-width: 375px)'  => [ 546, 308 ],
		];
	} else {
		$width = 1400;
		$height = 320;
		$picture = [
			'(min-width: 1024px)' => [ 1400, 320 ],
			'(min-width: 768px)'  => [ 624, 352 ],
			'(min-width: 375px)'  => [ 546, 308 ],
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

@if ( ! empty( $name ) )
	<tp-slider-slide @class($classes)>
		<x-media-lightbox
			name="{{ $name }}"
			path="{{ $video_url }}"
			title="{{ $title }}"
		>
			<x-image
				class="collage__video-thumbnail"
				:image_id="$image_id"
				:args="$image_args"
			/>

			<div class="collage__video-button-wrapper">
				<a role="button" class="collage__video-button">
					<x-svg name="play" />
				</a>
			</div>
		</x-media-lightbox>
	</tp-slider-slide>
@endif
