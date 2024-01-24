@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<footer class="lp-footer full-width">
	<div class="lp-footer__wrap">
		{!! $slot !!}
	</div>
</footer>
