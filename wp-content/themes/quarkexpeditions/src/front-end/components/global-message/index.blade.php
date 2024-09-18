@php
	if( empty( $slot ) ) {
		return;
	}
@endphp

<div class="global-message full-width">
	<x-content :content="$slot" />
</div>
