@props( [
	'icon' => 'star',
] )

@php
	$classes = [ 'icon-info-columns__icon' ];


	$classes[] = 'icon-info-columns__icon--' . match( $icon ) {
		'star'      => 'star',
		'compass'   => 'compass',
		'itinerary' => 'itinerary',
		'mountains' => 'mountains',
		'ship'      => 'ship',
		default     => 'star',
	};

@endphp


<div @class( $classes )>
</div>
