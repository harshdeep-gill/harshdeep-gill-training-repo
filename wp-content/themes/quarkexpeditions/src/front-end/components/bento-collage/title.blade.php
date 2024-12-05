@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$classes = [ 'bento-collage__card-title' ];
@endphp

<h4 @class($classes)>
	<x-escape :content="$title"/>
</h4>
