@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<tp-tabs-nav>
	<nav class="itinerary-details__tabs-nav">
		{!! $slot !!}
	</nav>
</tp-tabs-nav>
