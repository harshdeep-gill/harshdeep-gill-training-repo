@props( [
	'class' => '',
	'type'  => 'standard',
] )

@php
	if ( empty( $type ) ) {
		return;
	}

	$type = strtolower( $type );

	if ( ! in_array( $type, [ 'standard', 'premium', 'sold out' ] ) ) {
		return;
	}

	$classes = [
		'product-options-cards__badge',
		'overline'
	];

	$classes[] = 'product-options-cards__badge--' . match ( $type ) {
		'standard' => 'standard',
		'premium'  => 'premium',
		'sold out' => 'sold-out',
	};

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<x-escape :content="$type" />
</div>
