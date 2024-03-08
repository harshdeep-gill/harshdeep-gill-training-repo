@props( [
	'heading' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="media-content-card__content-column">
	@if ( ! empty( $heading ) )
		<p class="h4"><x-content :content="$heading" /></p>
	@endif
	{!! $slot !!}
</div>
