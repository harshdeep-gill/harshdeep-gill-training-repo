@props( [
	'image_id' => 0,
	'id'       => '',
] )

@php
	// Return if the image id is empty.
	if ( empty( $image_id ) ) {
		return;
	}

	// Image arguments.
	$image_args = [
		'size' =>       [
			'width'   => 600,
			'height'  => 676,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 576px) 600px', '312px' ],
			'widths' => [ 312, 600 ],
		],
	];

	// CSS classes for images.
	$classes = [ 'featured-media-accordions__image' ];
@endphp

<figure
	@class( $classes )
	data-hidden="yes"
	data-accordion-id="{!! esc_attr( $id ) !!}"
>
	<x-image
		:image_id="$image_id"
		:args="$image_args"
	/>
</figure>
