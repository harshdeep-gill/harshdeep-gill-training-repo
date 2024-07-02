@props( [
	'total_departures' => '',
	'from_date'        => '',
	'to_date'          => '',
] )

@php
	if ( empty( $total_departures ) && empty( $from_date ) && empty( $to_date ) ) {
		return;
	}
@endphp

<div class="expedition-details__departures">
	<p class="expedition-details__departures-label">{{ __( 'Departures', 'qrk' ) }}</p>
	<div class="expedition-details__departures-content">
		@if ( ! empty( $total_departures ) )
			<span><x-escape :content="$total_departures" /> {{ __( 'Departures between', 'qrk' ) }}</span>
		@endif
		@if ( ! empty( $from_date ) )
			<span><x-escape :content="$from_date" />  {{ __( 'to', 'qrk' ) }}</span>
		@endif
		@if ( ! empty( $to_date ) )
			<span><x-escape :content="$to_date" /></span>
		@endif
	</div>
</div>
