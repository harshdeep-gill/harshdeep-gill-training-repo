@props( [
	'sticky'         => false,
	'show_on_mobile' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'sidebar-grid__sidebar' ];

	if ( ! empty( $sticky ) ) {
		$classes[] = 'sidebar-grid__sidebar--sticky';
	}

	if ( ! empty( $show_on_mobile ) ) {
		$classes[] = 'sidebar-grid__sidebar--show-on-mobile';
	}
@endphp

<aside @class( $classes )>
	{!! $slot !!}
</aside>
