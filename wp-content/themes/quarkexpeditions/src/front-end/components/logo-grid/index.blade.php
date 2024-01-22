@props( [
	'justify'  => '',
	'size'     => ''
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'logo-grid' ];

	if(
		! empty( $justify ) && in_array(
			$justify,
			[ 'center', 'right', 'left' ],
			true
		)
	) {
		$classes[] = 'logo-grid--justify-' . $justify;
	} else {
		$classes[] = 'logo-grid--justify-left';
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
