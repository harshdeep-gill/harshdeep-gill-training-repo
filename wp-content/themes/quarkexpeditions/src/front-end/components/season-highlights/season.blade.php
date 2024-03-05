@props( [
	'title' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp


<div class="season-highlights__season">

	@if ( ! empty( $title ) )
		<p class="season-highlights__season-title h4">
			<x-escape :content="$title" />
		</p>
	@endif

	{!! $slot !!}
</div>
