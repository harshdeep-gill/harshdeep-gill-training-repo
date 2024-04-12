@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section class="lp-offer-masthead" full_width="true" seamless="true" background="true" background_color="black">
	{!! $slot !!}
</x-section>
