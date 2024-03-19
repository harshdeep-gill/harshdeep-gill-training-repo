@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="product-cards__info-ribbon body-small">
	<x-content :content="$slot" />
</div>
