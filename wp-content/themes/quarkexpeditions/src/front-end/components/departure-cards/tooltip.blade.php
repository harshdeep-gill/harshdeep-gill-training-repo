@props( [
	'title' => '',
	'icon'  => 'info',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="departure-cards__tooltip">
	<span class="departure-cards__tooltip-icon">
		<x-svg name="{{ $icon }}" />
	</span>

	<div class="departure-cards__tooltip-description">
		@if ( ! empty( $title ) )
			<p class="departure-cards__tooltip-title overline">
				<x-escape :content="$title" />
			</p>
		@endif

		{!! $slot !!}
	</div>
</div>
