@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'info-cards__card-description' ];
@endphp

<p @class( $classes )>
	<x-content :content="$slot" />
</p>
