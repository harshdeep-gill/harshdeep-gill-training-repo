@props( [
	'total_departures' => '',
	'date_range'       => '',
] )

@php
	if ( empty( $total_departures ) && empty( $date_range ) ) {
		return;
	}
@endphp

<div class="expedition-details__departures">
	<p class="expedition-details__departures-label">{{ __( 'Departures', 'qrk' ) }}</p>
	<div class="expedition-details__departures-content">
		@if ( ! empty( $total_departures ) )
			<span><x-escape :content="$total_departures"/> {{ _n( 'Departure', 'Departures', $total_departures, 'qrk' ) }}</span>
		@endif
		@if ( ! empty( $date_range ) )
			<span><x-escape :content="$date_range" /></span>
		@endif
	</div>
</div>
