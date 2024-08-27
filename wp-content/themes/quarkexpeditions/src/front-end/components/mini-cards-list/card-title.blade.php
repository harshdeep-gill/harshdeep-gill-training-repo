@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<div class="mini-cards-list__card-title">
	<x-escape :content="$title" />
</div>
