@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="hero__description">
	<x-content :content="$slot" />
</div>
