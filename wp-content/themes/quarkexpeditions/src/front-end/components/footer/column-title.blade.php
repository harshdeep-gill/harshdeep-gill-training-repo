@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<p class="footer__column-title" role="button">
	<x-escape :content="$title" />
</p>
