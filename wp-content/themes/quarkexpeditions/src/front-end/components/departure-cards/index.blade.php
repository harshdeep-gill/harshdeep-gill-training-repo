@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section class="departure-cards">
	{!! $slot !!}
</x-section>
