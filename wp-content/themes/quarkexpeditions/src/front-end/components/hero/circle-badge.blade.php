@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="hero__circle-badge color-context--dark">
	<p>
		<x-escape :content="$text" />
	</p>
</div>
