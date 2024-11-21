@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<p class="highlights__item-text">
	<x-escape :content="$text" />
</p>
