@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="product-cards__description">
	<x-content :content="$slot" />
</div>
