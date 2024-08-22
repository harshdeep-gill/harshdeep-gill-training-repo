@props( [
	'start_date' => '',
	'end_date'   => '',
	'year'       => '',
	'duration'   => '',
] )

<div class="dates-rates__expedition-dates">
	<x-svg name="calendar" />
	<div class="dates-rates__expedition-dates-content">
		<x-escape :content="$start_date" /> - <x-escape :content="$end_date" />, <x-escape :content="$year" />
		(<x-escape :content="$duration" /> {{ __( 'days', 'qrk' ) }})
	</div>
</div>
