@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="header__drawer-cta">
	<x-content :content="$slot" />
</div>
