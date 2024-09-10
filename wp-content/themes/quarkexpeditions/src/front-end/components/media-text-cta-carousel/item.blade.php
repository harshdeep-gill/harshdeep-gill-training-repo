@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<tp-slider-slide class="media-text-cta-carousel__slide">
	{!! $slot !!}
</tp-slider-slide>
