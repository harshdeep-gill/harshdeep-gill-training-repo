@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section>
	<div class="simple-cards grid">
		{!! $slot !!}
	</div>
</x-section>
