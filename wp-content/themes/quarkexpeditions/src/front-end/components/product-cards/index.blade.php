@props( [
	'align_center' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'product-cards', 'grid' ];

	var_dump( $align_center );

	if ( ! empty( $align_center ) ) {
		$classes[] = 'product-cards--align-center';
	} else {
		$classes[] = 'grid--cols-3';
	}
@endphp

<x-section>
	<div @class( $classes )>
		{!! $slot !!}
	</div>
</x-section>
