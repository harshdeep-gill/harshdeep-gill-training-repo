@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<footer class="footer full-width color-context--dark">
	<div class="footer__wrap">
		{!! $slot !!}
	</div>
</footer>
