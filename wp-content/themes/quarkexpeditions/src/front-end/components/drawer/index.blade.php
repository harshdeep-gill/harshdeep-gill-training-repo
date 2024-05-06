@props( [
	'id'                  => '',
	'class'               => '',
	'animation_direction' => '',
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
	data-animation_direction="{{ $animation_direction }}"
>
	<quark-drawer-content class="drawer__content">
		{!! $slot !!}
	</quark-drawer-content>
</quark-drawer>
