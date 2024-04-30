@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="drawer__body">
	{!! $slot !!}
</div>
