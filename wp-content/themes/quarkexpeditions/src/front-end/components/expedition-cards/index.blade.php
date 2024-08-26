@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section class="expedition-cards">
	{!! $slot !!}
</x-section>
