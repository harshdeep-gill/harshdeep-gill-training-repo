@props( [
	'image_id' => 0,
	'title'    => '',
	'url'      => '',
	'target'   => '',
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}

	$image_args = [
		'size' => [
			'width'   => 360,
			'height'  => 240,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 1280px) 600px', '100vw' ],
			'widths' => [ 320, 380, 480, 600 ],
		],
	];
@endphp

<article class="simple-cards__card">
	<x-maybe-link class="simple-cards__url" href="{{ $url }}" target="{{ $target }}">
		<figure class="simple-cards__image-wrap">
			<x-image
				class="simple-cards__image"
				:image_id="$image_id"
				:args="$image_args"
			/>
		</figure>

		@if ( ! empty( $title ) )
			<h3 class="simple-cards__title h5">
				<x-escape :content="$title" />
			</h3>
		@endif
	</x-maybe-link>
</article>
