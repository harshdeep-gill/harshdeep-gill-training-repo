@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'info-cards__card-overline', 'overline' ];
@endphp

<p @class( $classes )>
	<x-content :content="$slot" />
</p>
