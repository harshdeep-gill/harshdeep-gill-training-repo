@props( [
	'duration_date' => '',
	'duration'   => '',
] )

<div class="dates-rates__expedition-dates">
	<x-svg name="calendar" />
	<div class="dates-rates__expedition-dates-content">
		<x-escape :content="$duration_date" />
		(<x-escape :content="$duration" /> {{ __( 'days', 'qrk' ) }})
	</div>
</div>
