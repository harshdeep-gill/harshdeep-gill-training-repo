@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="product-cards__buttons">
	<x-content :content="$slot" />
</div>
