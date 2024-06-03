@props( [
	'icon'          => 'info',
	'open_position' => 'top', // 'top', 'bottom'.
] )

@php
	if ( empty( $icon ) || empty( $slot ) ) {
		return;
	}

	// Build Classes.
	$classes = [ 'tooltip__description' ];

	// Add class based on the position.
	if ( ! empty( $open_position ) ) {
		$classes[] = sprintf( 'tooltip__description--%s', $open_position ?? '' );
	} else {
		$classes[] = 'tooltip__description--top';
	}

	$classes = implode( ' ', $classes );
@endphp

<div class="tooltip">
	<span class="tooltip__icon">
		<x-svg name="{{ $icon }}" />
	</span>

	<div class="{{ $classes }}">
		{!! $slot !!}
	</div>
</div>
