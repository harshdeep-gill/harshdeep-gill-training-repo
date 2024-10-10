@props( [
	'title' => '',
	'align' => 'bottom',
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$classes = [ 'thumbnail-cards__card-title', 'h5' ];

	if ( ! empty( $align ) && in_array( $align, [ 'bottom', 'top' ], true ) ) {
		$classes[] = 'thumbnail-cards__card-title--align-' . $align;
	}
@endphp

<p @class($classes)>
	<x-escape :content="$title"/>
</p>
