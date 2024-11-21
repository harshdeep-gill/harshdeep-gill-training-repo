@props( [
	'class'        => '',
	'id'           => '',
	'name'         => '',
	'checked'      => '',
	'checkout_url' => '',
	'mask'         => '',
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
	id="{{ $id }}"
	checked="{{ $checked }}"
	mask="{{ $mask }}"
	value="{!! esc_url( $checkout_url ) !!}"
>
	{!! $slot !!}
</x-form.radio>
