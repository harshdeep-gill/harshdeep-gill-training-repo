@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section>
	<div class="simple-cards">
		{!! $slot !!}
	</div>
</x-section>
