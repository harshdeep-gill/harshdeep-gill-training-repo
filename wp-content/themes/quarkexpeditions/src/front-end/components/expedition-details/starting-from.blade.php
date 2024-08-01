@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="expedition-details__starting-from">
	<p class="expedition-details__starting-from-label">{{ __( 'Starting from', 'qrk' ) }}</p>
	<ul class="expedition-details__starting-from-content">
		{{ $slot }}
	</ul>
</div>
