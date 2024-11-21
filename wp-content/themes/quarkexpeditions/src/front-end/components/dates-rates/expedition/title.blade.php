@props( [
	'text' => '',
	'url'  => '',
] )

@php
	if ( empty( $text ) || empty( $url ) ) {
		return;
	}
@endphp

<a class="dates-rates__expedition-title" href="{{ $url }}">
	<x-escape :content="$text" />
</a>
