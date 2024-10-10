@props( [
	'id' => '',
	'class' => '',
] )

@php
	if ( empty( $id ) || empty( $slot ) ) {
		return;
	}

	$classes = [ 'product-options-cards__dialog', $class ];
@endphp

<x-dialog 
	id="{{ $id }}"
	@class( $classes )
>
	{!! $slot !!}
</x-dialog>
