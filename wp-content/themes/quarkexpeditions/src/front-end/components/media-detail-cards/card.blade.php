@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<article class="media-detail-cards__card grid">
	{!! $slot !!}
</article>
