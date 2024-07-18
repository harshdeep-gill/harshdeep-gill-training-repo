@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<div class="itinerary-details__header">
	<h3 class="itinerary-details__header-title"><x-escape :content="$title" /></h3>
	<button class="itinerary-details__button-close"><x-svg name="cross" /></button>
</div>
