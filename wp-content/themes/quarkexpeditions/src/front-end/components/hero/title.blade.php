@props( [
	'title'          => '',
	'text_color'     => '',
	'use_promo_font' => false,
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$classes = [ 'hero__title' ];

	if ( ! empty( $text_color ) && 'white' === $text_color ) {
		$classes[] = 'color-context--dark';
	}

	if ( ! empty( $use_promo_font ) ) {
		$classes[] = 'font-family--promo';
	}
@endphp

<h1 @class( $classes )>
	<x-content :content="$title" />
</h1>
