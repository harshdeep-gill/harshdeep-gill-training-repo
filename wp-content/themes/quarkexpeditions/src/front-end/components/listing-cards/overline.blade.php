@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<p class="listing-cards__overline overline">
	<x-escape :content="$text" />
</p>
