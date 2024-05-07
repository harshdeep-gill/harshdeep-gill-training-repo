@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="sidebar-grid__content">
	{!! $slot !!}
</div>