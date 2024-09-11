@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<tp-slider-slide class="product-cards__slide">
	<article class="product-cards__card">
		{!! $slot !!}
	</article>
</tp-slider-slide>
