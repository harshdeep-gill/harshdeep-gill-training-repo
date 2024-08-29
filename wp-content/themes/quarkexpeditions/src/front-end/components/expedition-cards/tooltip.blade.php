@props( [
	'title' => '',
	'icon'  => 'info',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="expedition-cards__tooltip">
	<span class="expedition-cards__tooltip-icon">
		<x-svg name="{{ $icon }}" />
	</span>

	<div class="expedition-cards__tooltip-description">
		@if ( ! empty( $title ) )
			<p class="expedition-cards__tooltip-title overline">
				<x-escape :content="$title" />
			</p>
		@endif

		{!! $slot !!}
	</div>
</div>
