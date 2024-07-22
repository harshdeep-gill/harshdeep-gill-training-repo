@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="media-detail-cards__content">
	<x-content :content="$slot" />
</div>
