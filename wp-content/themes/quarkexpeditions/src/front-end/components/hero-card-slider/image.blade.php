@props( [
	'class'       => '',
	'image_id'    => 0,
	'is_lightbox' => false,
] )

@php
	// Return if the image id is empty.
	if ( empty( $image_id ) ) {
		return;
	}

	// Image arguments
	$image_args = [
		'size' => [
			'width'   => 312,
			'height'  => 208,
			'picture' => [
				'(min-width: 1024px)' => [ 544, 584 ],
				'(min-width: 768px)'  => [ 1024, 577 ],
			],
		],
		'transform' => [
			'crop'    => 'fill',
			'gravity' => 'auto',
			'quality' => 90,
		],
	];


	// CSS classes for images.
	$classes = [ 'hero-card-slider__image' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	// Add lighbox class.
	if ( ! empty( $is_lightbox ) ) {
		$classes[] = 'hero-card-slider__image-lightbox';
	}
@endphp

@if ( $is_lightbox )
	<x-media-lightbox
		name="{{ $name ?? 'hero-card-slider' }}"
		:image_id="$image_id"
		title="{{ $title ?? '' }}"
		fullscreen_icon_position='top'
	>
		<figure @class( $classes )>
			<x-image
				:image_id="$image_id"
				:args="$image_args"
			/>
		</figure>
	</x-media-lightbox>
@else
	<figure @class( $classes )>
		<x-image
			:image_id="$image_id"
			:args="$image_args"
		/>
	</figure>
@endif
