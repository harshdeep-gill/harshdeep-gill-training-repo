@props( [
	'class'    => '',
	'subtitle' => '',
] )

@php
	if ( empty( $subtitle ) ) {
		return;
	}

	$classes = [ 'product-options-cards__room-subtitle' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<x-escape :content="$subtitle" />
</div>
