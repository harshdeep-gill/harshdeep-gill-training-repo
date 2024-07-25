@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="itinerary-details__tabs-content">
	{!! $slot !!}
</div>
