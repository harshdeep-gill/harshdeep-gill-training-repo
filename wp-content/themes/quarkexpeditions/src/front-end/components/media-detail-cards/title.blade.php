@props( [
	'title'         => '',
	'heading_level' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	// Title classes.
	$classes = [ 'media-detail-cards__title' ];
	if ( ! empty( $heading_level ) ) {
		$classes[] = sprintf( 'h%s', $heading_level );
	}
@endphp

<h3 @class( $classes )>
	<x-escape :content="$title" />
</h3>
