@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<nav class="itinerary-details__modal-nav">
	{!! $slot !!}
</nav>
