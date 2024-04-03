@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section class="lp-offer-masthead" full_width="true" seamless="true" padding="true">
	{!! $slot !!}
</x-section>
