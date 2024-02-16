@props( [
	'video_id' => '',
	'image_id' => '',
	'title'    => '',
] )

@php
	if ( empty( $slot ) || empty( $video_id ) ) {
		return;
	}

	$image_args = [
		'size' => [
			'width'   => 1400,
			'height'  => 800,
			// To maintain 16:9 aspect ratio
			'picture' => [
				'(min-width: 1600px)' => [ 1920, 1080 ],
				'(min-width: 1400px)' => [ 1600, 900 ],
				'(min-width: 1280px)' => [ 1400, 800 ],
				'(min-width: 1024px)' => [ 1200, 675 ],
				'(min-width: 768px)'  => [ 900, 506 ],
				'(min-width: 500px)'  => [ 700, 394 ],
				'(min-width: 375px)'  => [ 500, 281 ],
			],
		],
		'transform' => [
			'crop'    => 'fill',
			'quality' => 90,
		],
	];

	$video_embed_classes = [
		'video-icons-card__video',
		'wistia_embed',
		'seo=true',
		'videoFoam=true',
		'wistia_async_' . $video_id,
	];

	wp_enqueue_script( 'wistia-embed' );
@endphp

<quark-video-icons-card class="video-icons-card">
	@if ( ! empty( $title ) )
		<h2 class="video-icons-card__title"><x-escape :content="$title"/></h2>
	@endif

	<div class="video-icons-card__container">
		<div class="video-icons-card__overlay">
			@if ( ! empty( $title ) )
				<h2 class="video-icons-card__title"><x-escape :content="$title"/></h2>
			@endif
			{!! $slot !!}
		</div>

		@if ( ! empty( $image_id ) )
			<x-image
				:args="$image_args"
				class="video-icons-card__thumbnail"
				image_id="{{ $image_id }}"
			/>
		@endif

		<div @class( $video_embed_classes )></div>
	</div>

	{!! $slot !!}
</quark-video-icons-card>
