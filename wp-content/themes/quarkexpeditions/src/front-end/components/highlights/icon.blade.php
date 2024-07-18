@props( [
	'icon' => '',
] )

@php
	if ( empty( $icon ) ) {
		return;
	}
@endphp

<span class="highlights__icon">
	<x-svg name="{{ $icon }}" />
</span>
