@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="header__cta-buttons color-context--dark">
	<x-content :content="$slot" />
</div>
