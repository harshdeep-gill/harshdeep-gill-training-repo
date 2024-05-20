@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="footer__columns">
	{!! $slot !!}
</div>
