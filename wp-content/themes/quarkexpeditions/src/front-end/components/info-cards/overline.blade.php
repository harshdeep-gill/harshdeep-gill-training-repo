@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'info-cards__card-overline', 'overline' ];
@endphp

<div @class( $classes )>
	<x-escape :content="$slot->toHtml()" />
</div>
