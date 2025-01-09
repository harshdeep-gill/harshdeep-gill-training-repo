@props( [
	'text_color'     => '',
	'use_promo_font' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'hero__description' ];

	if ( ! empty( $text_color ) && 'white' === $text_color ) {
		$classes[] = 'color-context--dark';
	}

	if ( ! empty( $use_promo_font ) ) {
		$classes[] = 'font-family--promo';
	}
@endphp

<div @class( $classes )>
	<x-content :content="$slot" />
</div>
