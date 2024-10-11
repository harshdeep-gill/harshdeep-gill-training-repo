@php
	if( empty( $slot ) ) {
		return;
	}
@endphp

<header class="dialog__header">
	{!! $slot !!}
</header>
