@props( [
	'class'      => '',
	'video_id'   => 0,
	'video_type' => ''
] )

@php
	// Return if the video id is empty.
	if ( empty( $video_id ) ) {
		return;
	}

	// Video arguments.
	$video_args = [
		'transform' => [
			'width'   => 544,
			'height'  => 592,
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
		:video_type="$video_type"
		:loop="true"
		:controls="false"
	/>
</div>
