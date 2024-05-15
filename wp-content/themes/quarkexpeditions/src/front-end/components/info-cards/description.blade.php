@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'info-cards__card-description' ];
@endphp

<div @class( $classes )>
	<x-escape :content="$slot->toHtml()" />
</div>
