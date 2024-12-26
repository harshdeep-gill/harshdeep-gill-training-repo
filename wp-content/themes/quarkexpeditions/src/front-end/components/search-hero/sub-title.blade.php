@props( [
	'title'          => '',
	'text_color'     => '',
	'use_promo_font' => false,
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$classes = [ 'search-hero__sub-title', 'h5' ];

	if ( ! empty( $text_color ) && 'white' === $text_color ) {
		$classes[] = 'color-context--dark';
	}

	if ( ! empty( $use_promo_font ) ) {
		$classes[] = 'font-family--promo';
	}
@endphp

<h5 @class( $classes )>
	<x-escape :content="$title" />
</div>
