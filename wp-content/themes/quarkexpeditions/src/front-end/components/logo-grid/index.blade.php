@props( [
	'alignment'  => '',
	'size'       => ''
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'logo-grid' ];

	if(
		! empty( $alignment ) && in_array(
			$alignment,
			[ 'center', 'right', 'left' ],
			true
		)
	) {
		$classes[] = 'logo-grid--alignment-' . $alignment;
	} else {
		$classes[] = 'logo-grid--alignment-left';
	}

	if( ! empty( $size ) && in_array(
		$size,
		[ 'md', 'lg' ],
		true
	) ) {
		$classes[] = 'logo-grid--' . $size;
	}


@endphp

<div @class( $classes )>
	{!! $slot !!}
</div>
