@props( [
	'id'                  => '',
	'class'               => '',
	'animation_direction' => 'left',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'drawer', $class ];
@endphp

<quark-drawer
	@if ( ! empty( $id ) )
		id="{{ $id }}"
	@endif
	@class( $classes )
	overlay-click-close="yes"
	animation-direction="{{ $animation_direction }}"
>
	<quark-drawer-content class="drawer__content">
		<x-content :content="$slot" />
		<x-drawer.drawer-close />
	</quark-drawer-content>
</quark-drawer>
