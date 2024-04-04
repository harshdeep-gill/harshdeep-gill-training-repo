@props( [
	'title'            => '',
	'title_align'      => '',
	'heading_level'    => '3',
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

	// `section` tags must have a title.
	$tag_name = 'section';
	if ( empty( $title ) ) {
		$tag_name = 'div';
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
		$classes[] = 'section--seamless-with-padding';
		$classes[] = 'full-width';
		$wrap = true;

		// Add background color class, if set.
		if ( ! empty( $background_color ) ) {
			$background_colors = [ 'black', 'gray' ];

			if ( in_array( $background_color, $background_colors, true ) ) {
				$classes[] = sprintf( 'section--has-background-%s', $background_color );
			}
		}
	}

	if ( ! empty( $padding ) && true === boolval( $padding ) ) {
	    $classes[] = 'section--seamless-with-padding';
	}

	if ( ! empty( $full_width ) && true === boolval( $full_width ) ) {
		$classes[] = 'full-width';
	}

	$section_title_classes = [ 'section__title' ];

	if ( ! empty( $heading_level ) ) {
		$section_title_classes[] = sprintf( 'h%s', $heading_level );
	}

	if ( ! empty( $title_align ) && 'left' === $title_align ) {
		$section_title_classes[] = 'section__title--left';
	}
@endphp

<{{ $tag_name }}
	@if ( ! empty( $id ) )
		id="{{ $id }}"
	@endif
	@class( $classes )
	>
	@if ( ! empty( $wrap ) )
		<div class="wrap">
	@endif

	@if ( ! empty( $title ) )
		<h2 @class( $section_title_classes )>
			<x-content :content="$title" />
		</h2>
	@endif

	{!! $slot !!}

	@if ( ! empty( $wrap ) )
		</div>
	@endif
</{{ $tag_name }}>
