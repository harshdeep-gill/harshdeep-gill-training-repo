@props( [
	'sticky' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'sidebar-grid__sidebar' ];

	if ( ! empty( $sticky ) ) {
		$classes[] = 'sidebar-grid__sidebar--sticky';
	}
@endphp

<quark-sidebar @class( $classes ) data-is-sticky="{{ $sticky }}">
	{!! $slot !!}
</quark-sidebar>