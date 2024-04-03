@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="lp-offer-masthead__caption h5">
	<x-content :content="$slot" />
</div>
