@props( [
	'title'      => '',
	'text_color' => ''
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$classes = [ 'search-hero__title' ];

	if ( ! empty( $text_color ) && 'white' === $text_color ) {
		$classes[] = 'color-context--dark';
	}
@endphp

<h1 @class( $classes )>
	<x-content :content="$title" />
</h1>
