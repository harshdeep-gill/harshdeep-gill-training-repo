@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="hero__content">
	{!! $slot !!}
</div>
