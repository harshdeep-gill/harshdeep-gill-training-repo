@props( [
	'class' => '',
	'title' => '',
] )

@php
	if ( empty( $slot ) || empty( $title ) ) {
		return;
	}

	$classes = [ 'product-options-cards__rooms' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<h5 class="production-options-cards__rooms-title"><x-escape :content="$title" /></h5>
	{!! $slot !!}
</div>
