@props( [
	'url'   => '',
	'class' => ''
] )

@php
	$classes = [ 'options-button__default-option' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<x-button href="{{ $url ?? '' }}" size="big" @class( $classes )>
	{!! $slot !!}
</x-button>
