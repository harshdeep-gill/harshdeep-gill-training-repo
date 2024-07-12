@props( [
	'current_tab' => '',
	'update_url'  => 'no',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	quark_enqueue_style( 'tp-tabs' );
	quark_enqueue_script( 'tp-tabs' );
@endphp

<quark-itinerary-tabs class="itinerary-details">
	<tp-tabs class="itinerary-details__tabs" current-tab="{{ $current_tab }}" update-url="{{ $update_url }}">
		{!! $slot !!}
	</tp-tabs>
</quark-itinerary-tabs>
