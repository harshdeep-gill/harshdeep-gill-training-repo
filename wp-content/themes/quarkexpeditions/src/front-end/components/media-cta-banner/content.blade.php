@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="media-cta-banner__content">
	<x-content :content="$slot" />
</div>
