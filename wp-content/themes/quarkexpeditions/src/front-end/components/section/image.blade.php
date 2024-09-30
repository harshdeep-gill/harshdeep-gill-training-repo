@props( [
	'image_id'          => 0,
	'is_gradient'       => false,
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
		'transform' => [
			'quality' => 90,
		],
	];

	// Bulding calss.
	$classes = [ 'section__image-wrap', 'full-width' ];

	// Checking for gradient color and passing as a css varibale.
	if ( true === $is_gradient && in_array( $gradient_color, [ 'white', 'grey', 'black' ] ) ) {
		$gradient_color = "--section-gradient-color:$gradient_color";
	}

	// Adding a calss as per gradient position.
	if ( true === $is_gradient && in_array( $gradient_position, [ 'top', 'bottom', 'both' ] ) ) {
		$classes[] = "section__image-gradient-$gradient_position";
	}
@endphp

<div
	@class( $classes )
	style="{!! $gradient_color !!}"
>
	<x-image
		class="section__image"
		loading="eager"
		fetchpriority="high"
		:image_id="$image_id"
		:args="$image_args"
	/>
</div>
