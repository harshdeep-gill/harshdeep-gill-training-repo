@props( [
	'class' => '',
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$classes = [ 'product-options-cards__title' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<x-escape :content="$title" />
</div>
