@props( [
	'image_id'     => 0,
	'is_immersive' => false,
	'url'          => '',
] )

@php
	// Return if the image id is empty.
	if ( empty( $image_id ) ) {
		return;
	}

	// Image arguments.
	$image_args = [
		'size' =>       [
			'width'   => 360,
			'height'  => 240,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1280px) 360px', '(min-width: 1024px) 30vw', '(min-width: 576px) 60vw', '100vw' ],
			'widths' => [ 320, 380, 480, 600, 720 ],
		],
	];

	// CSS classes for images.
	$classes = [ 'product-cards__image' ];

	// Check if the `is_immersive` is set.
	if ( ! empty( $is_immersive ) && true === $is_immersive ) {
		$classes[] = 'product-cards__image-immersive';
	}

	// Check if the `url` is set.
	if ( ! empty( $url ) ) {
		$classes[] = 'product-cards__image-has-url';
	}
@endphp

<figure @class( $classes )>
	<x-maybe-link href="{{ $url }}" class="product-cards__image-link">
		<x-image
			:image_id="$image_id"
			:args="$image_args"
		/>
	</x-maybe-link>

	{!! $slot !!}
</figure>
