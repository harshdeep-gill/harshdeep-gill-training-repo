@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="dialog__body">
	{!! $slot !!}
</div>
