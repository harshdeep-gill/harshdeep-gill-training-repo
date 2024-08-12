@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<x-button type="button" size="big" class="departure-cards__cta">
	<x-escape :content="$text" />
</x-button>
