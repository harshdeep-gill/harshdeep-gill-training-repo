@props( [
	'ships' => [],
] )

@php
	if ( empty( $ships ) ) {
		return;
	}

	$ships = implode( ', ', $ships ?? '' )
@endphp

<div class="expedition-details__ships">
	<p class="expedition-details__ships-label">{{ __( 'Ships', 'qrk' ) }}</p>
	<div class="expedition-details__ships-content">
		{{ $ships }}
	</div>
</div>
