@props( [
	'class'        => '',
	'no_of_guests' => 1,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'product-options-cards__room' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	{!! $slot !!}
</div>
