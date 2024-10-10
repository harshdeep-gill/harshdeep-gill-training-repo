@props( [
	'sticky'           => false,
	'show_on_mobile'   => false,
	'sidebar_position' => 'right',
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

	if ( ! empty( $sidebar_position ) ) {
		if ( in_array( $sidebar_position, [ 'left', 'right' ], true ) ) {
			$classes[] = sprintf( 'sidebar-grid__sidebar-%s', $sidebar_position );
		}
	}
@endphp

<aside @class( $classes )>
	{!! $slot !!}
</aside>
