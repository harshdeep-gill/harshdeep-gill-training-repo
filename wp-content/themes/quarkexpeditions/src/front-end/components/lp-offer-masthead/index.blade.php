@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section class="lp-offer-masthead" full_width="true" seamless="true">
	{!! $slot !!}
</x-section>
