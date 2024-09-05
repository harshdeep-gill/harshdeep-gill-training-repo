@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="product-cards__specification-label">
	<x-content :content="$slot" />
</div>
