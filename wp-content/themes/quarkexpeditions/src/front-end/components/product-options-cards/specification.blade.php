@props( [
	'class' => '',
	'label' => '',
	'value' => '',
] )

@php
	if ( empty( $label ) || empty( $value ) ) {
		return;
	}

	$classes = [ 'product-options-cards__specification' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<div class="product-options-cards__specification-label">
		<x-escape :content="$label" />
	</div>
	<div class="product-options-cards__specification-value">
		<x-escape :content="$value" />
	</div>
</div>
