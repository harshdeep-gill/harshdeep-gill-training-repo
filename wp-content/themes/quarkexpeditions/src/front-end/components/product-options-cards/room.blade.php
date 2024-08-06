@props( [
	'class' => '',
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

<x-form.radio @class( $classes )>
	{!! $slot !!}
</x-form.radio>
