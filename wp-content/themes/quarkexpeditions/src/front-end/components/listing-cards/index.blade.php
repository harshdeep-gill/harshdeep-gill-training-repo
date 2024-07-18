@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section class="listing-cards">
	{!! $slot !!}
</x-section>
