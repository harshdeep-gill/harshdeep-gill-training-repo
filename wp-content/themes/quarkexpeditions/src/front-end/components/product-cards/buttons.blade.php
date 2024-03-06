@props( [
	'columns' => 2,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// classes.
	$classes = [ 'product-cards__buttons' ];

	if ( 2 === absint( $columns ) ) {
		$classes[] = sprintf( 'product-cards__buttons--cols-2' );
	}
@endphp

<div @class( $classes )>
	{!! $slot !!}
</div>
