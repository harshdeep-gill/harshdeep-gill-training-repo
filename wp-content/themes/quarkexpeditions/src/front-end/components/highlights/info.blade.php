@props( [
	'info' => '',
] )

@php
	if ( empty( $info ) ) {
		return;
	}
@endphp

<div class="highlights__info body-small">
	<x-content :content="$info" />
</div>
