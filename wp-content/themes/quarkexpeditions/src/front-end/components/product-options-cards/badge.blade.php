@props( [
	'class'  => '',
	'status' => 'A',
	'type'   => 'standard',
	'text'   => '',
] )

@php
	if ( empty( $status ) || empty( $text ) ) {
		return;
	}

	if ( ! in_array( $status, [ 'A', 'S', 'R' ] ) ) {
		return;
	}

	$type =  strtolower( $type );

	$classes = [
		'product-options-cards__badge',
		'overline'
	];

	if ( 'S' === $status || 'R' === $status ) {
		$classes[] = 'product-options-cards__badge--' . match ( $status ) {
			'S' => 'sold-out',
			'R' => 'please-call',
		};
	}

	if ( ! empty( $type ) && in_array( $type, [ 'standard', 'premium' ] ) ) {
		$classes[] = 'product-options-cards__badge--' . match ( $type ) {
			'standard' => 'standard',
			'premium'  => 'premium',
		};
	}

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<x-escape :content="$text" />
</div>
