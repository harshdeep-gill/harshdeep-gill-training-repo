@props( [
	'image_id'  => '',
	'video_url' => '',
	'size'      => '',
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
			'width'  => 1280,
			'height' => 720,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 720px) 720px', '100vw' ],
			'widths' => [ 320, 375, 450, 500, 576, 650, 980, 1024, 1200, 1440 ],
		],
	];
@endphp

<tp-slider-slide @class($classes)>
	<x-media-lightbox
		name="{{ $name }}"
		path="{{ $video_url }}"
		title="{{ $title }}"
	>
		<div class="collage__video-cover">
			<x-image
				class="collage__video-thumbnail"
				:image_id="$image_id"
				:args="$image_args"
			/>

			<div class="collage__video-button-wrapper">
				<button class="collage__video-button">
					<x-svg name="play" />
				</button>
			</div>
		</div>
	</x-media-lightbox>
</tp-slider-slide>
