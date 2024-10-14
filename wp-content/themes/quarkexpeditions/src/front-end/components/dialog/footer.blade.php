@php
	if( empty( $slot ) ) {
		return;
	}
@endphp

<footer class="dialog__footer">
	{!! $slot !!}
</footer>
