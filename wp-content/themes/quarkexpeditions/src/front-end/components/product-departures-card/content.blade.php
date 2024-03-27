@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="product-departures-card__content grid">
	{!! $slot !!}
</div>
