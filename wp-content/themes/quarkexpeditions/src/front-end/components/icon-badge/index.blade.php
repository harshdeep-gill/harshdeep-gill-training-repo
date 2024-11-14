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
@endphp

<div @class( $classes )>
	@if ( ! empty( $icon ) )
		<span class="icon-badge__icon">
			<x-svg name="{{ $icon }}" />
		</span>
	@endif
	<span class="icon-badge__description"><x-escape :content="$text" /></span>
</div>
