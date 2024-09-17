@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="mini-cards-list__card" tabindex="0">
	{!! $slot !!}
</div>
