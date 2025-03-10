@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<h1 class="expedition-details__title h1">
	<x-escape :content="$title" />
	{!! $slot !!}
</h1>
