@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section class="specifications">
	{!! $slot !!}
</x-section>
