@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

{{-- <x-section class="season-highlights" no_border="true"> --}}
	<div class="season-highlights">
		{!! $slot !!}
	</div>
{{-- </x-section> --}}
