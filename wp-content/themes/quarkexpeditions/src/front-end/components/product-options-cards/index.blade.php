@props( [
	'class' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'product-options-cards' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<quark-product-options-cards @class( $classes )>
	{!! $slot !!}
</quark-product-options-cards>
