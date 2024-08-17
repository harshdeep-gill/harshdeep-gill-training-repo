@props( [
	'start_date' => '',
	'end_date'   => '',
	'year'       => '',
	'duration'   => '',
] )

<div class="dates-rates__expedition-dates">
	<x-escape :content="$start_date" />
	<x-escape :content="$end_date" />
	<x-escape :content="$year" />
	<x-escape :content="$duration" />
</div>
