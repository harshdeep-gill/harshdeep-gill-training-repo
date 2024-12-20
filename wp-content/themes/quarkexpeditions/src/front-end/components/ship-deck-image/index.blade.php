@props( [
	'vertical_image_id'   => 0,
	'horizontal_image_id' => 0,
	'alt'                 => '',
] )

@php
	// Return if the image id is empty.
	if ( empty( $vertical_image_id ) && empty( $horizontal_image_id ) ) {
		return;
	}

	// Set default class.
	$class = 'ship-deck-image';

	// Image args.
	$horizontal_args = [
		'size' => [
			'width'  => 2400,
			'height' => 500,
		],
		'transform' => [
			'crop'    => 'fit',
			'quality' => 100,
			'format'  => 'svg',
		]
	];

	$vertical_args = [
		'size' => [
			'width'  => 500,
			'height' => 2400,
		],
		'transform' => [
			'crop'    => 'fit',
			'quality' => 100,
			'format'  => 'svg',
		]
	];

	// Classes.
	$classes = [ 'ship-deck-image' ];

	if ( ! empty( $vertical_image_id ) ) {
	    $classes[] = 'ship-deck-image--has-vertical-image';
	}
@endphp

<div @class( $classes )>
	@if ( ! empty( $horizontal_image_id ) )
		<figure @class( [ $class, 'ship-deck-image__horizontal' ] ) >
			<x-image
				:image_id="$horizontal_image_id"
				:alt="$alt"
				:args="$horizontal_args"
			/>
		</figure>
	@endif
	@if ( ! empty( $vertical_image_id ) )
		<figure @class( [ $class, 'ship-deck-image__vertical' ] ) >
			<x-image
				:image_id="$vertical_image_id"
				:alt="$alt"
				:args="$vertical_args"
			/>
		</figure>
	@endif
</div>
