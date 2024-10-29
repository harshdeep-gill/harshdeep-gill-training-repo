@php
	if ( empty( $slot ) ) {
		return;
	}

	quark_component_enqueue_assets( 'product-options-cards' );
@endphp

<quark-book-departures-ships class="book-departures-ships" loading="false">
	{!! $slot !!}
</quark-book-departures-ships>
