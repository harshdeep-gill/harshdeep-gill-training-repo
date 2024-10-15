@props( [
	'image_id' => 0,
	'link'     => '',
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}

	// Build image args.
	$image_args = [
		'size' => [
			'width'   => 335,
			'height'  => 450,
		],
		'responsive'  => [
			'sizes'   => [ '(min-width: 1200px) 1120px','(min-width: 1024px) 864px', '100vw' ],
			'widths'  => [ 335, 440, 540, 620, 864, 960, 1280 ],
		],
		'transform'   => [
			'crop'    => 'lfill',
			'quality' => 90,
			'gravity' => 'face',
		],
	];
@endphp

<figure class="featured-image typography-spacing">
	<x-maybe-link :href="$link">
		<x-image
			:image_id="$image_id"
			:args="$image_args"
		/>
	</x-maybe-link>
</figure>
