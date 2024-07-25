@props( [
	'class' => '',
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$classes = [ 'product-options-cards__card-details-title' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<h4>
		<x-escape :content="$title" />
	</h4>
	<button type="button">Hide details</button>
</div>
