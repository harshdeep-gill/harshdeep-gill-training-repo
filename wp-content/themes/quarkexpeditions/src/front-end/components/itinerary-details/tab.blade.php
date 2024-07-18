@props( [
	'id' => '',
] )

@php
	if ( empty( $slot ) || empty( $id ) ) {
		return;
	}
@endphp

<tp-tabs-tab class="itinerary-details__tab" id="{{ $id }}">
	{!! $slot !!}
</tp-tabs-tab>
