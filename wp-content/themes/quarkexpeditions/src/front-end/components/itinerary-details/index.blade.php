@props( [
	'current_tab' => '',
	'update_url'  => 'no',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-itinerary-tabs class="itinerary-details">
	<tp-tabs class="itinerary-details__tabs" current-tab="{{ $current_tab }}" update-url="{{ $update_url }}">
		{!! $slot !!}
	</tp-tabs>
</quark-itinerary-tabs>
