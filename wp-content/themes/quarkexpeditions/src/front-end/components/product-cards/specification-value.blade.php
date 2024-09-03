@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="product-cards__specification-value">
	<x-content :content="$slot" />
</div>
