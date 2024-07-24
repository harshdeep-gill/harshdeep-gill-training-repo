@props( [
	'class' => '',
	'title' => '',
] )

@aware( [
	'no_of_guests' => 1,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$no_of_guests = intval( $no_of_guests );

	$classes = [ 'product-options-cards__room' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	{!! $slot !!}
</div>
