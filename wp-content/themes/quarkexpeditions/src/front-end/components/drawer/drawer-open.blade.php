@props( [
	'drawer_id' => '',
	'class'     => '',
	'align'     => 'left',
] )

@php
	// This component should be wrapped in a button.
	if ( empty( $slot ) || empty( $drawer_id ) ) {
		return;
	}

	$classes = [ $class, 'drawer__drawer-open' ];

	if ( 'right' === $align ) {
		$classes[] = 'drawer__drawer-open--right';
	} elseif ( 'center' === $align ) {
		$classes[] = 'drawer__drawer-open--center';
	}
@endphp

<quark-drawer-open
	@class( $classes )
	drawer-id="{{ $drawer_id }}"
>
	{!! $slot !!}
</quark-drawer-open>
