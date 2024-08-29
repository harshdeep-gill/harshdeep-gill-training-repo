@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="expedition-cards__buttons">
	<x-content :content="$slot" />
</div>
