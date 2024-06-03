@props( [
	'background_color' => 'yellow',
	'text'             => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}

	$classes = [ 'info-cards__tag', 'overline' ];

	// Add background color class, if set.
	if ( ! empty( $background_color ) ) {
		$background_colors = [ 'yellow', 'magenta' ];

		if ( in_array( $background_color, $background_colors, true ) ) {
			$classes[] = sprintf( 'info-cards__tag--has-background-%s', $background_color );
		}
	}
@endphp

<div @class( $classes )>
	<x-escape :content="$text" />
</div>
