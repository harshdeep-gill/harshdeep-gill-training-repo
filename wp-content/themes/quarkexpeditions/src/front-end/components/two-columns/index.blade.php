@props( [
	'border' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'two-columns grid grid--cols-2' ];

	if ( true === $border ) {
		$classes[] = 'two-columns--has-border';
	}
@endphp

<x-section>
	<div @class( $classes )>
		{!! $slot !!}
	</div>
</x-section>
