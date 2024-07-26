@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="expedition-details__ships">
	<p class="expedition-details__ships-label">{{ __( 'Ships', 'qrk' ) }}</p>
	<ul class="expedition-details__ships-content">
		{!! $slot !!}
	</ul>
</div>
