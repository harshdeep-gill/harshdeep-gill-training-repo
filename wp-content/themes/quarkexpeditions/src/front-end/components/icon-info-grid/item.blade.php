@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<tp-slider-slide class="icon-info-grid__slide">
	<article class="icon-info-grid__item">
		{!! $slot !!}
	</article>
</tp-slider-slide>
