@php
	if ( empty( $slot ) ) {
		return;
	}

	quark_enqueue_script( 'querystring' );
@endphp

<quark-dates-rates class="dates-rates">
	{!! $slot !!}
</quark-dates-rates>
