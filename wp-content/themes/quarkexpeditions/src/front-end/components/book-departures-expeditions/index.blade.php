@php
	if ( empty( $slot ) ) {
		return;
	}

	quark_component_enqueue_assets( 'product-options-cards' );
@endphp

<quark-book-departures-expeditions class="book-departures-expeditions" loading="false">
	{!! $slot !!}
</quark-book-departures-expeditions>