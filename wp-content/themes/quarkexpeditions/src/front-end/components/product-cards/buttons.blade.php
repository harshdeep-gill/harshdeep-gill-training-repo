@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="product-cards__buttons product-cards__buttons--cols-2">
	{!! $slot !!}
</div>
