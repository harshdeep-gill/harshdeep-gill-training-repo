@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="media-text-cta__description">
	<x-content :content="$slot" />
</div>
