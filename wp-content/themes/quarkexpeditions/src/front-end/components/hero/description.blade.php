@props( [
	'text_color' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'hero__description' ];

	if ( ! empty( $text_color ) && 'white' === $text_color ) {
		$classes[] = 'color-context--dark';
	}
@endphp

<div @class( $classes )>
	<x-content :content="$slot" />
</div>
