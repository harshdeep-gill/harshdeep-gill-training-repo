@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="highlights__content">
	{!! $slot !!}
</div>
