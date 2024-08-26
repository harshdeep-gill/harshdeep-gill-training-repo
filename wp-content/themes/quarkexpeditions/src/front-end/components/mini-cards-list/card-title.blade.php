@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="mini-cards-list__card-title">
	<x-content :content="$slot" />
</div>
