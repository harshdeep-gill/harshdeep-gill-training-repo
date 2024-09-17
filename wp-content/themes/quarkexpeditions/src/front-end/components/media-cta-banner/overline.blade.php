@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="media-cta-banner__overline overline">
	<x-escape :content="$text" />
</div>
