@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<quark-book-departures-expeditions class="book-departures-expeditions" loading="false">
	{!! $slot !!}
</quark-book-departures-expeditions>