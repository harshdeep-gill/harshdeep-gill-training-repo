@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<p class="dates-rates__expedition-title">
	<x-escape :content="$text" />
</p>
