@props( [
	'drawer_id' => '',
	'class'    => '',
] )

@php
	// This component should be wrapped in a button.
	if ( empty( $slot ) || empty( $drawer_id ) ) {
		return;
	}
@endphp

<quark-drawer-open
	@class( [ $class, 'drawer__drawer-open' ] )
	drawer-id="{{ $drawer_id }}"
>
	{!! $slot !!}
</quark-drawer-open>
