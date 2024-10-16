@props( [
	'sub_title' => '',
] )

@php
	if ( empty( $sub_title ) ) {
		return;
	}
@endphp

<span class="expedition-details__sub-title h3">
	<x-escape :content="$sub_title" />
</span>
