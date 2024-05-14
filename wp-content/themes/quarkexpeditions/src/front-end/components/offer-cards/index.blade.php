@php
	if ( empty( $slot ) ) {
		return;
	}

	// Get cards count.
	$cards_count = quark_get_slot_child_count( $slot );

	// Cards classes.
	$classes = [ 'offer-cards', 'grid' ];
	$classes[] = sprintf( 'offer-cards--cols-%s', $cards_count );
@endphp

<x-section>
	<div @class( $classes )>
		{!! $slot !!}
	</div>
</x-section>
