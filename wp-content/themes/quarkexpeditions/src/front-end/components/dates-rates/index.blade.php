@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-dates-rates class="dates-rates">
	{!! $slot !!}
</quark-dates-rates>
