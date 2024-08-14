@props( [
	'url'   => '',
	'class' => '',
] )

@php
	if ( empty( $url ) ) {
		return;
	}

	$video_id = quark_get_wistia_id( $url );

	if ( empty( $video_id ) ) {
		return;
	}

	$video_embed_classes = [
		'wistia_embed',
		'seo=true',
		'videoFoam=true',
		'wistia_async_' . $video_id,
	];

	$classes = [ 'wisita-embed' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<quark-wistia-embed
	@class( $classes )
	video-id="{{ $video_id }}"
>
	<div @class( $video_embed_classes )></div>
</quark-wistia-embed>
