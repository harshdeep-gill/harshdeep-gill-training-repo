@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="hero__form color-context--dark">
	{!! $slot !!}
</div>
