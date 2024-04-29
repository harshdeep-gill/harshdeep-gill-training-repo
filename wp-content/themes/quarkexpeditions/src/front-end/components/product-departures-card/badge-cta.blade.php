@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="product-departures-card__badge-cta body-small">
	<x-escape :content="$text" />
</div>
