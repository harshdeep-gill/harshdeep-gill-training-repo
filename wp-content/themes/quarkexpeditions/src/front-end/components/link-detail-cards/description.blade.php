@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="link-detail-cards__description">
	<x-content :content="$slot" />
</div>
