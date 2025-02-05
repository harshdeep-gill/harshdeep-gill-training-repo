@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="overline departure-cards__tooltip-header">
	<x-content :content="$slot" />
</div>
