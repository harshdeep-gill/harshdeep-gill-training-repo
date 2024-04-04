@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="contact-cover-card__description">
	<x-content :content="$slot" />
</div>
