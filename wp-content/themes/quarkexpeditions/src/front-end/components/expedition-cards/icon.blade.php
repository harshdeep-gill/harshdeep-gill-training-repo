@props( [
	'icon' => '',
] )

@php
	if ( empty( $icon ) ) {
		return;
	}
@endphp

<div class="expedition-cards__icon">
	<span class="expedition-cards__icon-svg">
		<x-svg name="{{ $icon }}" />
	</span>

	<div class="expedition-cards__icon-content">
		{!! $slot !!}
	</div>
</div>
