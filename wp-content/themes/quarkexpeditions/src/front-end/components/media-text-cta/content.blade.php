@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="media-text-cta__content">
	<x-content :content="$slot" />
</div>
