@props( [
	'current_tab' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-itinerary-tabs class="itinerary-details">
	<tp-tabs class="itinerary-details__tabs" current-tab="{{ $current_tab }}" update-url="yes">
		{!! $slot !!}
	</tp-tabs>
</quark-itinerary-tabs>
