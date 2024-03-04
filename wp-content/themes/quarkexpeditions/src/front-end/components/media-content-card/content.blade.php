@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="media-content-card__content">
	{!! $slot !!}
</div>
