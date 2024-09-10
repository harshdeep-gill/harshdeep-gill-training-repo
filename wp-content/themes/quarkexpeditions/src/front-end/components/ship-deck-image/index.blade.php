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
@endphp

@if ( ! empty( $horizontal_image_id ) )
	<figure @class( [ $class, 'ship-deck-image--horizontal' ] ) >
		<x-image
			:image_id="$horizontal_image_id"
			:alt="$alt"
		/>
	</figure>
@endif
@if ( ! empty( $vertical_image_id ) )
	<figure @class( [ $class, 'ship-deck-image--vertical' ] ) >
		<x-image
			:image_id="$vertical_image_id"
			:alt="$alt"
		/>
	</figure>
@endif
