@props( [
	'icon' => 'info',
] )

@php
	if ( empty( $icon ) || empty( $slot ) ) {
		return;
	}
@endphp

<div class="tooltip">
	<span class="tooltip__icon">
		<x-svg name="{{ $icon }}" />
	</span>

	<div class="tooltip__description tooltip__description--top">
		{!! $slot !!}
	</div>
</div>
