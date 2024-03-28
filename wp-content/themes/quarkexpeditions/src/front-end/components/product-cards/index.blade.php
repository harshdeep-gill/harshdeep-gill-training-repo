@props( [
	'compact' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'product-cards', 'grid' ];

	if ( ! empty( $compact ) ) {
		$classes[] = 'grid--cols-2';
	} else {
		$classes[] = 'grid--cols-3';
	}
@endphp

<x-section>
	<div @class( $classes )>
		{!! $slot !!}
	</div>
</x-section>
