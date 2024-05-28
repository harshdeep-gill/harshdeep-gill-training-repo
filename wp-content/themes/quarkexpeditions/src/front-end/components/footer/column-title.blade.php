@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<p class="footer__column-title">
	<x-escape :content="$title" />
</p>
