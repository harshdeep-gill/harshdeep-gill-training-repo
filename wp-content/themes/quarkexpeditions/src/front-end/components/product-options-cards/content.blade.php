@props( [
	'class' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'product-options-cards__content' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	{!! $slot !!}
	<div class="product-options-cards__tooltip">
		<span>Inc. Transfer Package</span>
		<x-tooltip icon="info">
			Inc. Transfer Package
		</x-tooltip>
	</div>
</div>
