@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<article class="departure-cards__card">
	{!! $slot !!}
</article>
