@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section class="contact-cover-card" no_border="true">
	{!! $slot !!}
</x-section>
