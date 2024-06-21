@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="media-detail-cards__description">
	<x-content :content="$slot" />
</div>
