@props( [
	'icon' => '',
] )

@php
	if( empty( $icon ) ) {
		return;
	}
@endphp

<x-svg name="{{ $icon }}"/>
