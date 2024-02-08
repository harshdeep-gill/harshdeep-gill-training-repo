@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="modal__body">
	{!! $slot !!}
</div>
