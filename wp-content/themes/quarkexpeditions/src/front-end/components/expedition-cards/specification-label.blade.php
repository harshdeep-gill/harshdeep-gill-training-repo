@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="expedition-cards__specification-label">
	<x-content :content="$slot" />
</div>
