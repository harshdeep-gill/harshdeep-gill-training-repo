@props( [
	'date' => '',
] )

@php
	if ( empty( $date ) ) {
		return;
	}
@endphp

<h3 class="departure-cards__departing-on h4">
	<x-escape :content="$date" />
</h3>
