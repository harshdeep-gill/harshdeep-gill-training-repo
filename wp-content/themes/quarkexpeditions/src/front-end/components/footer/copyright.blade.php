@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="footer__copyright">
	<x-content :content="$slot" />
</div>
