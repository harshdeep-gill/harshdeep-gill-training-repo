@props( [
	'align' => 'left',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'product-cards', 'grid' ];

	if ( ! empty( $align ) && 'center' === $align ) {
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
