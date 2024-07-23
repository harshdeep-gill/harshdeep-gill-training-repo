@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<tp-slider-slide class="media-description-cards__slide">
	<article class="media-description-cards__card">
		{!! $slot !!}
	</article>
</tp-slider-slide>
