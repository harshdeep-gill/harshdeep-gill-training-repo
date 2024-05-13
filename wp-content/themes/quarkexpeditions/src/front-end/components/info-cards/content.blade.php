@props( [
	'position' => 'bottom'
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'info-cards__card-content' ];

	if ( ! empty( $position) && in_array( $position, [ 'top', 'bottom' ] ) ) {
		$classes[] = sprintf( 'info-cards__card-content--%s', $position );
	}
@endphp

<div @class($classes)>
	{!! $slot !!}
</div>
