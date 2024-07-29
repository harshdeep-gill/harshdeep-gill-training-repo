@props( [
	'id'                  => '',
	'class'               => '',
	'compact'             => false,
	'animation_direction' => 'left',
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
>
	<quark-drawer-content @class( $content_classes )>
		<x-content :content="$slot" />
		<x-drawer.drawer-close />
	</quark-drawer-content>
</quark-drawer>
