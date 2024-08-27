@props( [
	'date' => '',
] )

@php
	if ( empty( $date ) ) {
		return;
	}
@endphp

<div class="mini-cards-list__card-date">
	<x-escape :content="$date" />
</div>
