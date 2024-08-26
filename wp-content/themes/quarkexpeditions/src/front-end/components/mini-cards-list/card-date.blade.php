@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="mini-cards-list__card-date">
	<x-content :content="$slot" />
</div>
