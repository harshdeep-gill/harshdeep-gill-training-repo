@props( [
	'id'                  => '',
	'class'               => '',
	'compact'             => false,
	'animation_direction' => 'left',
	'close_on_desktop'    => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'drawer', $class ];
	$content_classes = [ 'drawer__content' ];

	if ( $compact ) {
		$content_classes[] = 'drawer__content--compact';
	}
@endphp

<quark-drawer
	@if ( ! empty( $id ) )
		id="{{ $id }}"
	@endif
	@class( $classes )
	overlay-click-close="yes"
	animation-direction="{{ $animation_direction }}"
	close-on-desktop="{{ $close_on_desktop }}"
>
	<quark-drawer-content @class( $content_classes )>
		<x-content :content="$slot" />
		<x-drawer.drawer-close />
	</quark-drawer-content>
</quark-drawer>
