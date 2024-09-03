@props( [
	'icon' => '',
] )

@php
	if ( empty( $icon ) ) {
		return;
	}
@endphp

<div class="product-cards__icon-content">
	<span class="product-cards__icon-svg">
		<x-svg name="{{ $icon }}" />
	</span>

	<div class="product-cards__icon-text">
		{!! $slot !!}
	</div>
</div>
