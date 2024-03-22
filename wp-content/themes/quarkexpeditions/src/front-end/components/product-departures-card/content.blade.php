@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="product-departures-card__content grid grid--cols-2">
	{!! $slot !!}
</div>
