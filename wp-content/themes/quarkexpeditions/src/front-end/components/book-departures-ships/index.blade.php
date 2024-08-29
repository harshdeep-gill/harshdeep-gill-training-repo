@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-book-departures-ships class="book-departures-ships" loading="false">
	{!! $slot !!}
</quark-book-departures-ships>
