@props( [
	'title' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="departure-cards__offers">
	@if ( ! empty( $title ) )
		<p class="departure-cards__offers-title"><x-escape :content="$title" /></p>
	@endif

	<ul class="departure-cards__offers-list">
		{!! $slot !!}
	</ul>
</div>
