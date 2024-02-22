@props( [
	'icon' => '',
] )

@php
	if ( empty( $icon ) ) {
		return;
	}
@endphp

<span class="icon-columns__icon">
	<x-svg name="{{ $icon }}" />
</span>
