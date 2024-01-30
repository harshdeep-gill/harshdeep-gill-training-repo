@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-section class="hero" full_width="true" seamless="true">
	<div class="hero__wrap">
		{!! $slot !!}
	</div>
</x-section>
