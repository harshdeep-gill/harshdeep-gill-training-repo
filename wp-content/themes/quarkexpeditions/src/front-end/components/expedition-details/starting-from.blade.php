@props( [
	'starting_from' => [],
] )

@php
	if ( empty( $starting_from ) ) {
		return;
	}

	$starting_from = implode( ', ', $starting_from ?? '' )
@endphp

<div class="expedition-details__starting-from">
	<p class="expedition-details__starting-from-label">{{ __( 'Starting from', 'qrk' ) }}</p>
	<div class="expedition-details__starting-from-content">
		{{ $starting_from }}
	</div>
</div>
