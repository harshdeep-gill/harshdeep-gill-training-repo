@props( [
	'title' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="media-content-card__content-column">
	@if ( ! empty( $title ) )
		<p class="h4"><x-escape :content="$title" /></p>
	@endif
	{!! $slot !!}
</div>
