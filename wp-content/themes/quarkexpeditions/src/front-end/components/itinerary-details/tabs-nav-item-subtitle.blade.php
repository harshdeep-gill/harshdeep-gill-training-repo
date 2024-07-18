@props( [
	'subtitle' => '',
] )

@php
	if ( empty( $subtitle ) ) {
		return;
	}
@endphp

<p class="itinerary-details__tabs-nav-item-subtitle">
	<x-escape :content="$subtitle" />
</p>
