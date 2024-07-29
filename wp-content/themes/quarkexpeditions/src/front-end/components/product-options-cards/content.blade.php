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
		<span>{{ __( 'Inc. Transfer Package', 'qrk' ) }}</span>
		<x-tooltip icon="info">
			{{ __( 'Inc. Transfer Package', 'qrk' ) }}
		</x-tooltip>
	</div>
</div>
