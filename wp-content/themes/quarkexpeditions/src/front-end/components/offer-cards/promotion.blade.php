@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="offer-cards__promotion">
	<x-escape :content="$text" />
</div>
