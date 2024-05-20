@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<footer class="footer full-width">
	<div class="footer__wrap">
		{!! $slot !!}
	</div>
</footer>
