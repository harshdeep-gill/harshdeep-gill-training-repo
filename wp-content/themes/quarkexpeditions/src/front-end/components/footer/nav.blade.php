@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<ul class="footer__nav">
	{!! $slot !!}
</ul>
