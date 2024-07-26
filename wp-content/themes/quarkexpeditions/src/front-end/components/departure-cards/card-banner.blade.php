@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="departure-cards__text">
	<x-svg name="shield" />
	<span><x-escape :content="$text" /></span>
</div>
