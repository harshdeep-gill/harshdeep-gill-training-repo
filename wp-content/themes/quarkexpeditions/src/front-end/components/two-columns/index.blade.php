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
@endphp

<x-section>
	<div @class( $classes )>
		{!! $slot !!}
	</div>
</x-section>
