@php
	if ( empty( $slot ) ) {
		return;
	}

	quark_enqueue_script( 'querystring' );
	quark_component_enqueue_assets( 'tooltip' );
	quark_component_enqueue_assets( 'drawer' );
	quark_enqueue_script( 'popover-polyfill' );
@endphp

<quark-dates-rates class="dates-rates">
	<h1 class="dates-rates__title"><x-escape :content="__( 'Dates and Rates', 'qrk' )" /></h1>
	{!! $slot !!}
</quark-dates-rates>
