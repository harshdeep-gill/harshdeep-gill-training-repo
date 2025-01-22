@props( [
	'class'            => '',
	'id'               => '',
	'seamless'         => false,
	'full_width'       => false,
	'narrow'           => false,
	'background'       => false,
	'background_color' => 'gray',
	'padding'          => false,
	'wrap'             => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'section' ];
	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	if ( ! empty( $seamless ) && true === boolval( $seamless ) ) {
		$classes[] = 'section--seamless';
	}

	if ( ! empty( $narrow ) && true === boolval( $narrow ) ) {
		$classes[] = 'section--narrow';
	}

	if ( ! empty( $background ) && true === boolval( $background ) ) {
		$classes[] = 'section--has-background';
		$classes[] = 'section--seamless';
		$classes[] = 'full-width';
		$wrap = true;

		// Add background color class, if set.
		if ( ! empty( $background_color ) ) {
			$background_colors = [ 'black', 'gray' ];

			if ( in_array( $background_color, $background_colors, true ) ) {
				$classes[] = sprintf( 'section--has-background-%s', $background_color );

				// If background color is black, set color context dark.
				if ( 'black' === $background_color ) {
					$classes[] = 'color-context--dark';
				}
			}
		}
	}

	if ( ! empty( $padding ) && true === boolval( $padding ) ) {
	    $classes[] = 'section--seamless-with-padding';
	}

	if ( ! empty( $full_width ) && true === boolval( $full_width ) ) {
		$classes[] = 'full-width';
	}
@endphp

<section
	@if ( ! empty( $id ) )
		id="{{ $id }}"
	@endif
	@class( $classes )
	{{ $attributes->filter( fn ( $value, $key ) => str_starts_with( $key, 'data-' ) ) }}
	>
	@if ( ! empty( $wrap ) )
		<div class="wrap">
	@endif

	{!! $slot !!}

	@if ( ! empty( $wrap ) )
		</div>
	@endif
</section>
