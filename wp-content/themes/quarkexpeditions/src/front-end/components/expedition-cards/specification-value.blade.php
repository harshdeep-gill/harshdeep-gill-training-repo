@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="expedition-cards__specification-value">
	<x-content :content="$slot" />
</div>
