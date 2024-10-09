@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="highlights__item-overline overline">
	<x-content :content="$slot" />
</div>
