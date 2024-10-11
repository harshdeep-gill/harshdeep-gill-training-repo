@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="mini-cards-list">
	@if ( ! empty( $slot ) )
		{!! $slot !!}
	@endif
</div>
