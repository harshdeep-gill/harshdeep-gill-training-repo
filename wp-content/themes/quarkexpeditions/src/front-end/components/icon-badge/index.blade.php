@props( [
	'background_color' => '',
	'icon'             => '',
	'text'             => '',
	'class'            => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}

	$background_color_class = quark_get_background_color_class( $background_color );

	if ( empty( $background_color_class ) ) {
		$background_color_class = 'has-background--attention-100';
	}

	$classes = [ 'icon-badge', $background_color_class, $class ];

	if ( empty( $icon ) ) {
		$icon = 'alert';
	}
@endphp

<div @class( $classes )>
	<span class="icon-badge-icon">
		<x-svg name="{{ $icon }}" />
	</span>
	<span class="icon-badge-description"><x-escape :content="$text" /></span>
</div>
