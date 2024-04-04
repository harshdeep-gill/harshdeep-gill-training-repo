@php
	if ( empty( $slot ) ) {
		return;
	}

	// Get cards count.
	$cards_count = quark_get_slot_child_count( $slot );

	$classes = [ 'product-cards' ];

	if ( 2 === $cards_count ) {
		$classes[] = 'product-cards--cols-2';
	} else {
		$classes[] = 'grid--cols-3';
	}
	$classes[] = 'grid';
@endphp

<x-section>
	<div @class( $classes )>
		{!! $slot !!}
	</div>
</x-section>
