@props( [
	'class'            => '',
	'horizontal_align' => '',
	'vertical_align'   => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [
		'buttons',
		'typography-spacing',
		sprintf( 'buttons--horizontal-%s', $horizontal_align ) => ! empty( $horizontal_align ),
		sprintf( 'buttons--vertical-%s', $vertical_align )     => ! empty( $vertical_align ),
		$class,
	];
@endphp

<div @class( $classes )>
	{!! $slot !!}
</div>
