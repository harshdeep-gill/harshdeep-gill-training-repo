@props( [
	'title'         => '',
	'align'         => '',
	'heading_level' => '3',
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$title_classes = [ 'section__title' ];

	if ( ! empty( $heading_level ) ) {
		$title_classes[] = sprintf( 'h%s', $heading_level );
	}

	if ( ! empty( $align ) && 'left' === $align ) {
		$title_classes[] = 'section__title--left';
	}
@endphp

<h2 @class( $title_classes )>
	<x-content :content="$title" />
</h2>
