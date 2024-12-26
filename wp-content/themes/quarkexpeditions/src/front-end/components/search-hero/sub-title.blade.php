@props( [
	'title'          => '',
	'text_color'     => '',
	'use_promo_font' => false,
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$classes = [ 'search-hero__sub-title' ];

	if ( ! empty( $text_color ) && 'white' === $text_color ) {
		$classes[] = 'color-context--dark';
	}

	if ( ! empty( $use_promo_font ) ) {
		$classes[] = 'font-family--promo';
	}
@endphp

<div @class( $classes )>
	<h5 class="h5"><x-escape :content="$title" /></h5>
</div>
