@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-dynamic-phone-number>
	{!! $slot !!}
</quark-dynamic-phone-number>
