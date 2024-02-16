@props( [
	'url'      => '',
	'image_id' => '',
	'title'    => '',
] )

@php
	if ( empty( $slot ) ) {
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
@endphp

<quark-video-icons-card class="video-icons-card">
	<div class="video-icons-card__overlay">
		@if ( ! empty( $title ) )
			<h2 class="video-icons-card__title">{{ $title }}</h2>
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

	<video
		width="1120"
		height="630"
		src="{{ $url }}"
		class="video-icons-card__video"
	></video>
</quark-video-icons-card>
