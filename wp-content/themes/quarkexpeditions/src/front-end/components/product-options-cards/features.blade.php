@props( [
	'class' => '',
	'title' => '',
] )

@php
	if ( empty( $slot ) || empty( $title ) ) {
		return;
	}

	$classes = [ 'product-options-cards__features' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<strong><x-escape :content="$title"/></strong>
	<x-content :content="$slot" />
</div>
