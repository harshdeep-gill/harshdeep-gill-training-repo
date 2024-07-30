@props( [
	'drawer_id' => '',
	'class'    => '',
	'align'    => 'left',
] )

@php
	// This component should be wrapped in a button.
	if ( empty( $slot ) || empty( $drawer_id ) ) {
		return;
	}

	$class = [ 'drawer__drawer-open' ];

	if ( 'right' === $align ) {
		$class[] = 'drawer__drawer-open--right';
	} elseif ( 'center' === $align ) {
		$class[] = 'drawer__drawer-open--center';
	}
@endphp

<quark-drawer-open
	@class( $class )
	drawer-id="{{ $drawer_id }}"
>
	{!! $slot !!}
</quark-drawer-open>
