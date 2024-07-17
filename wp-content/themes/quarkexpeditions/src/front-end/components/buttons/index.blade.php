@props( [
	'horizontal_align' => '',
	'vertical_align'   => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
	$classes = [
		'buttons',
		sprintf( 'buttons--horizontal-%s', $horizontal_align ) => ! empty( $horizontal_align ),
		sprintf( 'buttons--vertical-%s', $vertical_align )     => ! empty( $vertical_align ),
	];
@endphp

<div @class( $classes )>
	{!! $slot !!}
</div>
