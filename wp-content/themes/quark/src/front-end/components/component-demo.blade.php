@props( [
	'keys' => [],
] )

@php
	// Get current demo.
	$current_demo = array_filter( array_map( 'trim', explode( ',', $_GET['demo'] ?? '' ) ) );

	// If there's an active demo, and if that doesn't match any of our keys, lets ignore this component.
	if ( ! empty( $current_demo ) && ! array_intersect( $keys ?? [], $current_demo ) ) {
	    return;
	}
@endphp

{!! $slot !!}
