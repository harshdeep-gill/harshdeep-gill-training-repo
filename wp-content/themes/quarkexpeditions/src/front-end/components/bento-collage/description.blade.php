@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'bento-collage__card-description' ];
@endphp

<div @class( $classes )>
	<x-content :content="$slot" />
</div>
