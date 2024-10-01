@props( [
	'class'        => '',
	'name'         => '',
	'checked'      => '',
	'checkout_url' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'product-options-cards__room' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<x-form.radio
	@class( $classes )
	name="{{ $name }}"
	checked="{{ $checked }}"
	data-checkout-url="{{ $checkout_url }}"
>
	{!! $slot !!}
</x-form.radio>
