@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}

	$classes = [ 'thumbnail-cards__tag', 'overline' ];
@endphp

<div @class( $classes )>
	<x-escape :content="$text" />
</div>
