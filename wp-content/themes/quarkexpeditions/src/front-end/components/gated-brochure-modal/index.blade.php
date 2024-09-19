@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="gated-brochure-modal">
	{!! $slot !!}
</div>
