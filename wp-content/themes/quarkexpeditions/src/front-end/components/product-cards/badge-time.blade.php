@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="product-cards__badge-time body-small">
	<x-svg name="time" />
	<x-escape :content="$text" />
</div>
