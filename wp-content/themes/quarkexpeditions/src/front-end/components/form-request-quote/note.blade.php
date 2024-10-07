@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="form-request-quote__note body-small">
	{!! $slot !!}
</div>
