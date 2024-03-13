@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="media-text-cta__badge body-small">
	<x-escape :content="$text" />
</div>
