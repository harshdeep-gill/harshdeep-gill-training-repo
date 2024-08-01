@props( [
	'class' => '',
	'name'  => '',
] )

@php
	if ( empty( $name ) ) {
		return;
	}

	$classes = [ 'product-options-cards__discount' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<x-escape :content="$name" />
</div>
