@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<ul class="expedition-cards__options-list">
	<x-content :content="$slot" />
</ul>
