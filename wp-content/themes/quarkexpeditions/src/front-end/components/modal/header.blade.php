@php
	if( empty( $slot ) ) {
		return;
	}
@endphp

<header class="modal__header">
	{!! $slot !!}
</header>
