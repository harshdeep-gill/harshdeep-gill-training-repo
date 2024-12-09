@props( [
	'position' => 'bottom'
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'bento-collage__card-content' ];

	if ( ! empty( $position) && in_array( $position, [ 'top', 'bottom' ] ) ) {
		$classes[] = sprintf( 'bento-collage__card-content--%s', $position );
	}
@endphp

<div @class($classes)>
	{!! $slot !!}
</div>
