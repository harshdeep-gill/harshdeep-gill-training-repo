@props( [
	'is_compact' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'media-content-card' ];

	if ( ! empty( $is_compact ) && true === boolval( $is_compact ) ) {
		$classes[] = 'media-content-card--compact';
	}
@endphp

<div @class( $classes )>
	{!! $slot !!}
</div>