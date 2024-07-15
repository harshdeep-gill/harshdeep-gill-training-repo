@props( [
	'image_id' => 0,
	'size'     => 'large',
	'url'      => '',
] )

@php
	// Return if the image id is empty.
	if ( empty( $image_id ) ) {
		return;
	}

	if ( 'small' === $size ) {
		// Image arguments.
		$image_args = [
			'size' =>       [
				'width'   => 432,
				'height'  => 192,
			],
			'responsive' => [
				'sizes'  => [ '(min-width: 1024px) 50vw', '100vw' ],
				'widths' => [ 320, 400, 480, 600, 800 ],
			],
		];
	} else {
		// Image arguments.
		$image_args = [
			'size' =>       [
				'width'   => 400,
				'height'  => 574,
			],
			'responsive' => [
				'sizes'  => [ '(min-width: 1280px) 400px', '(min-width: 1024px) 50vw', '100vw' ],
				'widths' => [ 320, 400, 480, 600, 800 ],
			],
		];
	}

@endphp

<x-maybe-link href="{{ $url }}">
	<div class="header__nav-item-featured color-context--dark">
		<x-image
			:image_id="$image_id"
			:args="$image_args"
			class="header__nav-item-featured-image"
		/>

		<div class="header__nav-item-featured-content">
			{!! $slot !!}
		</div>
	</div>
</x-maybe-link>
