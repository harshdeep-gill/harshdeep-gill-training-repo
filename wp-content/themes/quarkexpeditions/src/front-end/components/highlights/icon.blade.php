@props( [
	'icon'   => '',
	'border' => false,
] )

@php
	if ( empty( $icon ) ) {
		return;
	}

	$class = [ 'highlights__icon' ];

	if ( $border ) {
		$class[] = 'highlights__icon--border';
	}
@endphp

<span @class( $class )>
	<x-svg name="{{ $icon }}" />
</span>
