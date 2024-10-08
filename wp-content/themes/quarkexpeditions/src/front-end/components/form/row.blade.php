@props( [
	'class' => '',
] )

@php
	$classes = [ 'form-row' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	{{ $slot }}
</div>
