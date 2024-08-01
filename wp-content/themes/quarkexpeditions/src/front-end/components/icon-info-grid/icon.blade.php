@props( [
	'icon' => '',
] )

@php
	if( empty( $icon ) ) {
		return;
	}
@endphp

<div class="icon-info-grid__icon">
	<x-svg name="{{ $icon }}" />
</div>
