@php
	if( empty( $slot ) ) {
		return;
	}
@endphp

<footer class="drawer__footer">
	{!! $slot !!}
</footer>
