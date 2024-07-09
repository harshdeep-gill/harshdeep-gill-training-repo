@props( [
	'name' => '',
] )

@php
	if( empty( $name ) ) {
		return;
	}
@endphp

<span class="footer__icon">
	<x-svg name="{{ $name }}" />
</span>
