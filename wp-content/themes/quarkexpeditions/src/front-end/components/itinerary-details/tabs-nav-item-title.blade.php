@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<p class="itinerary-details__tabs-nav-item-title">
	<x-escape :content="$title" />
</p>
