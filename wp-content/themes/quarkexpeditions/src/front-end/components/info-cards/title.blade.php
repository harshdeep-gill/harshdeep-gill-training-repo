@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$classes = [ 'info-cards__card-title' ];
@endphp

<h4 @class($classes)>
	<x-escape :content="$title"/>
</h4>
