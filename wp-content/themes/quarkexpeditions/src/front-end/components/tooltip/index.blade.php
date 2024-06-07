@props( [
	'icon' => 'info',
] )

@php
	if ( empty( $icon ) || empty( $slot ) ) {
		return;
	}
@endphp

<quark-tooltip class="tooltip">
	<span class="tooltip__icon">
		<x-svg name="{{ $icon }}" />
	</span>

	<div class="tooltip__description">
		{!! $slot !!}
	</div>
</quark-tooltip>
