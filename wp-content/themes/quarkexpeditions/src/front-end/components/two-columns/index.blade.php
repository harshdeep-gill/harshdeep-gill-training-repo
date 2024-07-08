@props( [
	'border' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'two-columns grid' ];

	if ( true === $border ) {
		$classes[] = 'two-columns--has-border';
	}

	// Get child count.
	$child_count = quark_get_slot_child_count( $slot );

	if ( 2 > $child_count ) {
		$classes[] = 'two-columns--only-child';
	}
@endphp

<x-section>
	<div @class( $classes )>
		{!! $slot !!}
	</div>
</x-section>
