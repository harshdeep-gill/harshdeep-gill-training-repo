@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<p class="dates-rates__expedition-overline overline">
	<x-escape :content="$text" />
</p>
