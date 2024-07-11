@props( [
	'image_id' => 0,
	'caption'  => '',
	'name'     => '',
] )

@php
	if ( empty( $image_id ) || empty( $name ) ) {
		return;
	}

	$image_args = [
		'size' => [
			'width'   => 544,
			'height'  => 426,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1280px) 544px', '(min-width: 1024px) 25vw', '(min-width: 576px) 50vw', '100vw' ],
			'widths' => [ 440, 560, 660, 840 ],
		],
		'transform' => [
			'crop' => 'fill',
		]
	];

	// Get the full URL of the image.
	$full_url = quark_dynamic_image_url(
		[
			'id'        => $image_id,
			'transform' => [
				'quality' => 90,
			],
		]
	);
@endphp

<x-media-lightbox
	name="{{ $name ?? '' }}"
	path="{{ $full_url }}"
	title="{{ $caption }}"
>
	<x-image
		:image_id="$image_id"
		:args="$image_args"
	/>
</x-media-lightbox>
