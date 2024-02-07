@php
	if( empty( $slot ) ) {
		return;
	}
@endphp

<footer class="modal__footer">
	{!! $slot !!}
</footer>
