@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<tp-tabs-nav-item class="itinerary-details__tabs-nav-item">
	<a class="itinerary-details__tabs-nav-link body-text-ui-small" href="#{{ $id }}">
		{!! $slot !!}
	</a>
</tp-tabs-nav-item>
