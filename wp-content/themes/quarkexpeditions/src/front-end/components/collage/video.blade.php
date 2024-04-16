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

	$image_args = [
		'size' => [
			'width'  => 1152,
			'height' => 648,
		],
		'responsive' => [
			'sizes'  => match( $size ) {
				'small'   => [ '(min-width: 360px) 400px', '100vw' ],
				'medium'  => [ '(min-width: 560px) 600px', '100vw' ],
				'large'   => [ '(min-width: 840px) 900px', '100vw' ],
				'x-large' => [ '(min-width: 992px) 1152px', '100vw' ],
			},
			'widths' => [ 360, 450, 576, 768, 992, 1120 ],
		],
		'transform' => [
			'crop' => 'fill',
		]
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
				<div role="button" class="collage__video-button">
					<x-svg name="play" />
				</div>
			</div>
		</x-media-lightbox>
	</tp-slider-slide>
@endif
