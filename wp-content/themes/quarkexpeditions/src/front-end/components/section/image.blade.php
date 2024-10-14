@props( [
	'image_id'          => 0,
	'gradient_color'    => '',
	'gradient_position' => 'top',
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}

	// Build image args.
	$image_args = [
		'size' => [
			'width'   => 1400,
			'height'  => 800,
			'picture' => [
				'(min-width: 1600px)' => [ 1920, 1080 ],
				'(min-width: 1400px)' => [ 1600, 900 ],
				'(min-width: 1280px)' => [ 1400, 800 ],
				'(min-width: 1024px)' => [ 1200, 800 ],
				'(min-width: 768px)'  => [ 900, 550 ],
				'(min-width: 500px)'  => [ 700, 550 ],
				'(min-width: 375px)'  => [ 550, 550 ],
			],
		],
	];

	// Bulding calss.
	$classes = [ 'section__image-wrap', 'full-width' ];

	// Checking for gradient color and passing as a css varibale.
	if ( ! empty( $gradient_color ) && in_array( $gradient_color, [ 'white', 'gray', 'black' ] ) ) {
		if ( "gray" === $gradient_color ) {
			$gradient_color = "--section-gradient-color:var(--color-$gradient_color-5)";
		} else {
			$gradient_color = "--section-gradient-color:var(--color-$gradient_color)";
		}
	}

	// Adding a calss as per gradient position.
	if ( ! empty( $gradient_position ) && in_array( $gradient_position, [ 'top', 'bottom', 'both' ] ) ) {
		$classes[] = "section__image-gradient-$gradient_position";
	}
@endphp

<div
	@class( $classes )
	style="{!! esc_attr( $gradient_color ) !!}"
>
	<x-image
		class="section__image"
		:image_id="$image_id"
		:args="$image_args"
	/>
</div>
