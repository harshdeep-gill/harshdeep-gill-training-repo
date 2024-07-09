@php
	$attributes = ! empty( $attributes ) ? $attributes->getAttributes() : [];
	if ( empty( $attributes['video_id'] ) ) {
		return;
	}

	$attributes['args']['id'] = $attributes['video_id'];

	$autoplay = $attributes['autoplay'] ?? false;
	$loop     = $attributes['loop'] ?? false;
	$controls = $attributes['controls'] ?? true;

	$video_type = 'video/mp4';

	if ( ! empty( $attributes['video_type'] ) ) {
		$video_type = $attributes['video_type'];
	}

	$video_size = [
		'height' => 300,
		'width'  => 300,
	];

	if ( empty( $attributes['args']['transform'] ) ) {
		$attributes['args']['transform'] = [
			'height'  => 300,
			'width'   => 300,
			'quality' => 80,
			'crop'    => 'fit',
		];
	} else {
		$video_size['height'] = $attributes['args']['transform']['height'];
		$video_size['width'] = $attributes['args']['transform']['width'];
	}

	$video_url = quark_dynamic_video_url( $attributes['args'] )
@endphp

@if ( ! empty( $video_url ) )
	<video
		width="{{ $video_size['width'] }}"
		height="{{ $video_size['height'] }}"

		@if ( ! empty( $autoplay ) )
			autoplay
		@endif

		@if ( ! empty( $loop ) )
			loop
		@endif

		@if ( ! empty( $controls ) )
			controls
		@endif

		playsinline
	>
		<source src="{{ $video_url }}" type="{{ $video_type }}">
	</video>
@endif

