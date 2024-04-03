@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section>
	<div class="product-cards grid grid--cols-3">
		{!! $slot !!}
	</div>
</x-section>
