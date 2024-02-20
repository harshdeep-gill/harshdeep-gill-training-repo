@props( [
	'title' => '',
	'icon'  => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="season-highlights__highlight">
	@if ( ! empty( $icon ) )
		<x-svg name="{{ $icon }}" />
	@endif

	@if ( ! empty( $title ) )
		<span class="season-highlights__highlight-title">
			<x-escape :content="$title" />
		</span>
	@endif
</div>
