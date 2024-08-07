@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="media-text-cta__overline overline">
	<x-content :content="$slot" />
</div>
