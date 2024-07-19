@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section class="media-detail-cards">
	{!! $slot !!}
</x-section>
