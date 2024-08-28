@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<p class="expedition-cards__date">
	<x-escape :content="$slot" />
</p>
