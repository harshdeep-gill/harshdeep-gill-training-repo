@props( [
	'class' => '',
	'type'  => 'standard',
] )

@php
	if ( empty( $type ) ) {
		return;
	}

	if ( ! in_array( $type, [ 'standard', 'premium', 'sold out' ] ) ) {
		return;
	}

	$classes = [ 'product-options-cards__badge', 'overline' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<x-escape :content="$type" />
</div>
