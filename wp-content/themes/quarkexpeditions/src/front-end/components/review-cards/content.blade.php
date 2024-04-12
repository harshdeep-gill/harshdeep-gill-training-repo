@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="review-cards__content">
	{!! $slot !!}
</div>
