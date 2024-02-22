@props( [
	'name' => '',
] )

@php
	if ( empty( $name ) ) {
		return;
	}
@endphp

<span class="lp-footer__icon">
	<x-svg name="{{ $name }}" />
</span>
