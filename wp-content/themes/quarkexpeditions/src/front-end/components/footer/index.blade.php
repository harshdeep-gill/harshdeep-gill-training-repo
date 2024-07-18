@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<footer class="footer full-width color-context--dark">
	{!! $slot !!}
</footer>
