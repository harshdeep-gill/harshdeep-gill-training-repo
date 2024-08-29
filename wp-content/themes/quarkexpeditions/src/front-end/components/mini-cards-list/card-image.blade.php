@props( [
	'image_id' => 0,
] )


@php
	if ( empty( $image_id ) ) {
		return;
	}
@endphp

<figure class="mini-cards-list__card-image">
	<x-image
		:image_id="$image_id"
		:args="[
			'size'      => [
				'width'  => 72,
				'height' => 72,
			],
			'transform' => [
				'crop'    => 'fill',
				'quality' => 90,
				'gravity' => 'auto',
			]
		]"
	/>
</figure>
