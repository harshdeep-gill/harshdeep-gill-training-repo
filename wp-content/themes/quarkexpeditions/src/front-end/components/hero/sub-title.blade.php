@props( [
	'title'      => '',
	'text_color' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$classes = [ 'hero__sub-title' ];

	if ( ! empty( $text_color ) && 'white' === $text_color ) {
		$classes[] = 'color-context--dark';
	}
@endphp

<div @class( $classes )>
	<h5 class="h5"><x-escape :content="$title" /></h5>
</div>
