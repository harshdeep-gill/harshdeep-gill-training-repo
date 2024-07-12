@props( [
	'class'      => '',
	'video_id'   => 0,
] )

@php
	// Return if the video id is empty.
	if ( empty( $video_id ) ) {
		return;
	}

	// Video arguments.
	$video_args = [
		'transform' => [
			'width'   => 700,
			'height'  => 700,
			'crop'    => 'fit',
			'quality' => 100,
		],
	];

	// CSS classes for video.
	$classes = [ 'hero-card-slider__video' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<x-video
		:video_id="$video_id"
		:args="$video_args"
		:loop="true"
		:controls="false"
	/>
</div>
