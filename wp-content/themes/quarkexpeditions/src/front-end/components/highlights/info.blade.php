@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="highlights__info body-small">
	<x-content :content="$slot" />
</div>
