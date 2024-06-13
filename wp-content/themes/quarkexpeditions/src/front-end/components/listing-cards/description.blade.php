@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="listing-cards__description">
	<x-content :content="$slot" />
</div>
