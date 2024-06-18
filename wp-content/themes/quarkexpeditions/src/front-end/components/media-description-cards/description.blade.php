@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="media-description-cards__description">
	<x-content :content="$slot" />
</div>
