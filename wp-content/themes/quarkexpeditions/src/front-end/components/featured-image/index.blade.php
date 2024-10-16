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
			'width'   => 1120,
			'height'  => 516,
			'picture' => [
				'(min-width: 1280px)' => [ 1120, 526 ],
				'(min-width: 768px)'  => [ 864, 516 ],
				'(min-width: 375px)'  => [ 312, 450 ],
			],
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
