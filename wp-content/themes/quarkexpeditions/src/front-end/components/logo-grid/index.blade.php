@props( [
	'justify'  => '',
	'gap'    => '1'
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

	if( ! empty( $gap ) && in_array( $gap, [ '1' , '2', '3' ], true ) ) {
		$classes[] = 'logo-grid--gap-' . $gap;
	}

@endphp

<div @class( $classes )>
	{!! $slot !!}
</div>
