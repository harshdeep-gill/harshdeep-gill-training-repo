@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<tp-slider-slide class="review-cards__card">
	{!! $slot !!}
</tp-slider-slide>
