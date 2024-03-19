@props( [
	'title'     => '',
	'name'      => '',
	'image_id'  => '',
	'video_url' => '',
] )

@php
	if ( empty( $image_id ) || empty( $video_url ) ) {
		return;
	}

	$image_args = [
		'size' => [
			'width'   => 660,
			'height'  => 440,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1280px) 450px', '(min-width: 1024px) 25vw', '(min-width: 576px) 50vw', '100vw' ],
			'widths' => [ 440, 560, 660, 840 ],
		],
	];
@endphp

<div class="media-text-cta__image-wrap">
	<x-media-lightbox
		name="{{ $name }}"
		path="{{ $video_url }}"
		:title="$title"
		:media="false"
	>
		<div class="media-text-cta__video">
			<x-image
				class="media-text-cta__image"
				:image_id="$image_id"
				:args="$image_args"
			/>

			<button class="media-text-cta__video-button">
				<x-svg name="play" />
			</button>
		</div>
	</x-media-lightbox>
</div>
