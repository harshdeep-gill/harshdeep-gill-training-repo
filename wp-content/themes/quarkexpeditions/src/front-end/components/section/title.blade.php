@props( [
	'title'         => '',
	'align'         => '',
	'heading_level' => '2',
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$title_classes = [ 'section__title' ];
	$tag_name      = 'h2';

	if ( ! empty( $heading_level ) ) {
		$tag_name = sprintf( 'h%s', $heading_level );
	}

	if ( ! empty( $align ) && 'left' === $align ) {
		$title_classes[] = 'section__title--left';
	}

@endphp

<{{ $tag_name }} @class( $title_classes )>
	<x-content :content="$title" />
</{{ $tag_name }}>
