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
	<x-maybe-link class="simple-cards__url" href="{{ $url }}" target="{{ $target }}" fallback_tag="div">
		<figure class="simple-cards__image-wrap">
			<x-image
				class="simple-cards__image"
				:image_id="$image_id"
				:args="$image_args"
			/>
		</figure>

		<div class="simple-cards__overlay">
			@if ( ! empty( $title ) )
				<h5 class="simple-cards__title">
					<x-escape :content="$title" />
				</h5>
			@endif
		</div>
	</x-maybe-link>
</article>
