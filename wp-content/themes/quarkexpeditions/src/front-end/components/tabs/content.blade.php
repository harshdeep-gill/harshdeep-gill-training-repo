@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="tabs__content">
	{!! $slot !!}
</div>
