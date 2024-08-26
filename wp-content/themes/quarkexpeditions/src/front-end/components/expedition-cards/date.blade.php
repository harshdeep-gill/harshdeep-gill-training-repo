@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<p class="expedition-cards__date">
	<x-content :content="$slot" />
</p>
