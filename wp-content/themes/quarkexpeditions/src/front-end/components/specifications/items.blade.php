@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="specifications__items grid grid--cols-3">
	{!! $slot !!}
</div>
