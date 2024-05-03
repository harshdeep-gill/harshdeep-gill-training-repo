@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="product-departures-card__departure-dates">
	<x-content :content="$slot" />
</div>
