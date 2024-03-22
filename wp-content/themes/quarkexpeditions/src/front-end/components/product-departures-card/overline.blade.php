@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="product-departures-card__overline">
	<x-escape :content="$text" />
</div>
