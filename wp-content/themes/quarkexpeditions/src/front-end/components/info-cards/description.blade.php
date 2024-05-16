@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'info-cards__card-description' ];
@endphp

<div @class( $classes )>
	<x-content :content="$slot" />
</div>
