@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="offer-cards__help-text body-small">
	<x-content :content="$slot" />
</div>
