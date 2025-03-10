@props( [
	'media_align' => 'left',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'media-text-cta grid' ];

	if ( ! empty( $media_align ) ) {
		$classes[] = sprintf( 'media-text-cta--media-align-%s', $media_align );
	}
@endphp

<div @class( $classes )>
	{!! $slot !!}
</div>
