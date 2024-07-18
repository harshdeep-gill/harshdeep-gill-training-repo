@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<article class="listing-cards__card">
	{!! $slot !!}
</article>
