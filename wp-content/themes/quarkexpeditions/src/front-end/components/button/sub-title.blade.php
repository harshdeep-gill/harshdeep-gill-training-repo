@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<div class="btn__sub-title">
	<x-escape :content="$title"/>
</div>
