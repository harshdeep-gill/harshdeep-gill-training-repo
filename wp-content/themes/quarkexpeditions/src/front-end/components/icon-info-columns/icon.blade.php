@props( [
	'icon' => '',
] )

@php
	if( empty( $icon ) ) {
		return;
	}
@endphp

<span class="icon-info-columns__icon">
	<x-svg name="{{ $icon }}" />
</span>
