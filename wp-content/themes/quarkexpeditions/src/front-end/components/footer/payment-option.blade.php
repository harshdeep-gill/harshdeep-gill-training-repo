@props( [
	'type' => '',
] )

@php
	if ( empty( $type ) ) {
		return;
	}
@endphp

<li class="footer__payment-option">
	<x-svg name="payment/{{ $type }}" />
</li>
