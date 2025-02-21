@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="overline expedition-cards__tooltip-header">
	<x-content :content="$slot" />
</div>
