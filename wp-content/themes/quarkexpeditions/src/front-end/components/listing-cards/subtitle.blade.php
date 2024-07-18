@props( [
	'subtitle' => '',
] )

@php
	if ( empty( $subtitle ) ) {
		return;
	}
@endphp

<p class="listing-cards__subtitle h4">
	<x-escape :content="$subtitle" />
</p>
