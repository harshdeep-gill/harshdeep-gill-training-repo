@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<h3 class="expedition-cards__title h4">
	<x-escape :content="$slot" />
</h3>
