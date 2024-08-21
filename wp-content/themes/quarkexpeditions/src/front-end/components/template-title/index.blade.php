@props( [
	'title' => '',
	'class' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$classes = [ 'template-title' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<h1 @class( $classes )>
	<x-escape :content="$title" />
</h1>
