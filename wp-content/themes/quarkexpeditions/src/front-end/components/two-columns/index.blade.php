@props( [
	'border'          => false,
	'stack_on_tablet' => false,
	'id'              => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'two-columns grid' ];

	if ( true === $border ) {
		$classes[] = 'two-columns--has-border';
	}

	if ( true === $stack_on_tablet ) {
		$classes[] = 'two-columns--stack-on-tablet';
	}

	// Get child count.
	$child_count = quark_get_slot_child_count( $slot );

	if ( 2 > $child_count ) {
		$classes[] = 'two-columns--only-child';
	}

	var_dump( $id );
@endphp

<x-section id="{{ $id }}">
	<div @class( $classes )>
		{!! $slot !!}
	</div>
</x-section>
