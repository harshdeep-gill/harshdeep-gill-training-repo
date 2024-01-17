@props( [
	'class' => '',
	'label' => '',
] )

@php
	$classes = [ 'checkbox' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<label @class( $classes )>
	<input type="checkbox" {{ $attributes->filter( fn ( $value, $key ) => $key !== 'label' ) }}>
	<x-escape :content="$label"/>
</label>
