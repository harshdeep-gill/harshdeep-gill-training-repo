@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<ul class="footer__payment-options">
	{!! $slot !!}
</ul>
